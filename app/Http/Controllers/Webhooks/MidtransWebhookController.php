<?php

namespace App\Http\Controllers\Webhooks;

use App\Enums\OrderStatus;
use App\Enums\StockReferenceType;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\InventoryService;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller MidtransWebhookController
 * Menangani notifikasi webhook pembayaran Midtrans secara asinkron (Settlement, Expire, Cancel, Deny)
 * dengan SHA512 Signature Verification dan Idempotency Guard.
 */
class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans,
        protected InventoryService $inventory,
        protected WhatsAppService $whatsApp
    ) {}

    /**
     * Handle incoming Midtrans Webhook notification
     * POST /api/webhooks/midtrans
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received: ', $payload);

        $orderNumber       = $payload['order_id'] ?? null;
        $statusCode        = $payload['status_code'] ?? null;
        $grossAmount       = $payload['gross_amount'] ?? null;
        $signatureKey      = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;
        $transactionId     = $payload['transaction_id'] ?? null;

        if (!$orderNumber || !$statusCode || !$grossAmount || !$signatureKey) {
            Log::warning('Midtrans Webhook: Incomplete payload data');
            return response()->json(['status' => 'error', 'message' => 'Incomplete data'], 400);
        }

        // 1. Verifikasi SHA512 Signature
        if (!$this->midtrans->verifySignature($orderNumber, $statusCode, $grossAmount, $signatureKey)) {
            Log::error('Midtrans Webhook: Invalid SHA512 signature for order ' . $orderNumber);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        // Cek apakah ini transaksi Langganan Ngizan Premium (PREM-...)
        if (str_starts_with($orderNumber, 'PREM-')) {
            return $this->handlePremiumSubscription($orderNumber, $transactionStatus, $transactionId, $payload);
        }

        // 2. Cari Data Order
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            Log::error('Midtrans Webhook: Order not found: ' . $orderNumber);
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        // 3. Idempotency Guard: Jangan proses ulang jika sudah lunas
        if ($order->status === OrderStatus::PAID && in_array($transactionStatus, ['settlement', 'capture'])) {
            Log::info("Midtrans Webhook: Order {$orderNumber} already marked as PAID. Skipping.");
            return response()->json(['status' => 'success', 'message' => 'Already processed']);
        }

        DB::beginTransaction();
        try {
            // 4. Update atau Buat Record Payment
            $payment = Payment::firstOrNew(['order_id' => $order->id]);
            $payment->transaction_id     = $transactionId;
            $payment->payment_type       = $paymentType;
            $payment->gross_amount       = (float) $grossAmount;
            $payment->raw_payload        = $payload;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->transaction_status = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $payment->transaction_status = 'settlement';
                    $payment->paid_at            = now();
                    $order->update(['status' => OrderStatus::PAID, 'paid_at' => now()]);
                }
            } else if ($transactionStatus == 'settlement') {
                $payment->transaction_status = 'settlement';
                $payment->paid_at            = now();
                $order->update(['status' => OrderStatus::PAID, 'paid_at' => now()]);
            } else if ($transactionStatus == 'pending') {
                $payment->transaction_status = 'pending';
            } else if (in_array($transactionStatus, ['deny', 'cancel'])) {
                $payment->transaction_status = 'cancel';
                $order->update(['status' => OrderStatus::CANCELLED, 'cancelled_at' => now()]);

                // Pulihkan Stok Varian
                $this->inventory->restoreStock($order, StockReferenceType::RESTOCK_CANCELLED);
            } else if ($transactionStatus == 'expire') {
                $payment->transaction_status = 'expire';
                $order->update(['status' => OrderStatus::EXPIRED, 'cancelled_at' => now()]);

                // Pulihkan Stok Varian
                $this->inventory->restoreStock($order, StockReferenceType::RESTOCK_EXPIRED);
            }

            $payment->save();
            DB::commit();

            // 5. Kirim Notifikasi WhatsApp Pembayaran Lunas
            if ($order->status === OrderStatus::PAID) {
                try {
                    $this->whatsApp->sendPaymentReceived($order);
                } catch (Exception $e) {
                    Log::warning('WhatsApp Send Payment Received Failed: ' . $e->getMessage());
                }
            }

            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Midtrans Webhook Processing Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Memproses callback webhook untuk transaksi langganan Ngizan Premium
     */
    protected function handlePremiumSubscription(string $subscriptionCode, ?string $transactionStatus, ?string $transactionId, array $payload): JsonResponse
    {
        $subscription = \App\Models\PremiumSubscription::where('subscription_code', $subscriptionCode)->first();
        if (!$subscription) {
            Log::error('Midtrans Webhook: Premium subscription not found: ' . $subscriptionCode);
            return response()->json(['status' => 'error', 'message' => 'Subscription not found'], 404);
        }

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $duration = $subscription->duration_days ?: 365;
            $subscription->update([
                'payment_status' => 'settlement',
                'paid_at'        => now(),
                'expires_at'     => now()->addDays($duration),
            ]);

            $user = $subscription->user;
            if ($user) {
                $user->update([
                    'is_premium'    => true,
                    'premium_until' => now()->addDays($duration),
                ]);

                // Kirim notifikasi WA selamat bergabung
                if ($user->phone) {
                    try {
                        $msg = "Selamat bergabung di *Ngizan Premium*, {$user->name}! 💎\n\n"
                            . "Keanggotaan Anda telah aktif selama 1 tahun. Anda kini otomatis menikmati diskon 5% untuk semua jersey di Ngizan Apparel.\n\n"
                            . "Cek koleksi jersey terbaru di: " . route('shop.index');
                        $this->whatsApp->sendMessage($user->phone, $msg);
                    } catch (\Throwable $e) {
                        Log::warning("Gagal kirim WA premium: " . $e->getMessage());
                    }
                }
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
            $subscription->update([
                'payment_status' => $transactionStatus,
            ]);
        }

        return response()->json(['status' => 'success', 'success' => true, 'message' => 'Subscription processed']);
    }
}
