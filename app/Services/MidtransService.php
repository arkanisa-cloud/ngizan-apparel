<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service MidtransService
 * Mengintegrasikan Midtrans Payment Gateway (Snap API & Webhook Verification)
 */
class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapApiUrl;

    public function __construct()
    {
        $this->serverKey    = (string) config('services.midtrans.server_key');
        $this->clientKey    = (string) config('services.midtrans.client_key');
        $this->isProduction = (bool) config('services.midtrans.is_production', false);
        $this->snapApiUrl   = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * 1. Buat Snap Token & Redirect URL untuk Pembayaran Order
     *
     * @param Order $order Model pesanan
     * @return array ['snap_token' => ..., 'redirect_url' => ...]
     * @throws Exception
     */
    public function createSnapToken(Order $order): array
    {
        // Susun daftar item pesanan
        $itemDetails = [];
        foreach ($order->items as $item) {
            $itemDetails[] = [
                'id'       => 'VAR-' . ($item->product_variant_id ?? $item->id),
                'price'    => (int) ($item->unit_price + $item->custom_fee),
                'quantity' => (int) $item->quantity,
                'name'     => substr($item->product_name . ' (' . $item->size . ' - ' . $item->type . ')', 0, 50),
            ];
        }

        // Tambahkan ongkos kirim jika ada
        if ($order->shipping_cost > 0) {
            $courierName = $order->courier_code ? strtoupper($order->courier_code) : 'KURIR';
            $itemDetails[] = [
                'id'       => 'SHIPPING-FEE',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => "Ongkos Kirim {$courierName} ({$order->courier_service_code})",
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->grand_total,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email'      => $order->customer_email,
                'phone'      => $order->customer_phone,
            ],
            'item_details' => $itemDetails,
            'callbacks' => [
                'finish' => url("/customer/orders/{$order->id}"),
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'hours',
                'duration'   => 2,
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($this->snapApiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $snapToken = $data['token'] ?? null;
                $redirectUrl = $data['redirect_url'] ?? null;

                // Simpan atau update ke tabel payments
                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'gross_amount'       => $order->grand_total,
                        'snap_token'         => $snapToken,
                        'snap_redirect_url'  => $redirectUrl,
                        'transaction_status' => 'pending',
                    ]
                );

                return [
                    'snap_token'   => $snapToken,
                    'redirect_url' => $redirectUrl,
                ];
            }

            Log::error('Midtrans Snap Error Response: ' . $response->body(), ['order' => $order->order_number]);
            throw new Exception($response->json('error_messages.0') ?? 'Gagal membuat Snap Token dari Midtrans.');
        } catch (Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage(), ['order' => $order->order_number]);
            throw $e;
        }
    }

    /**
     * 2. Verifikasi Keaslian Signature SHA512 dari Webhook Midtrans
     *
     * @param string $orderId
     * @param string $statusCode
     * @param string $grossAmount
     * @param string $signatureKey
     * @return bool
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $calculatedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $this->serverKey
        );

        return hash_equals($calculatedSignature, $signatureKey);
    }

    /**
     * 3. Dapatkan Status Transaksi Langsung dari Midtrans API v2
     *
     * @param string $orderNumber
     * @return array|null
     */
    public function getTransactionStatus(string $orderNumber): ?array
    {
        $statusApiUrl = $this->isProduction
            ? "https://api.midtrans.com/v2/{$orderNumber}/status"
            : "https://api.sandbox.midtrans.com/v2/{$orderNumber}/status";

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->timeout(10)
                ->get($statusApiUrl);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("Midtrans getTransactionStatus for {$orderNumber} returned {$response->status()}: " . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error("Midtrans getTransactionStatus exception for {$orderNumber}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 4. Sinkronisasi Real-Time Status Pesanan Langsung dari Midtrans API
     * (Sangat krusial saat webhook tidak tembus di localhost atau sebelum webhook masuk)
     *
     * @param Order $order
     * @return bool True jika status berubah atau lunas
     */
    public function syncOrderStatus(Order $order): bool
    {
        $payload = $this->getTransactionStatus($order->order_number);
        if (!$payload) {
            return false;
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;
        $transactionId     = $payload['transaction_id'] ?? null;
        $grossAmount       = $payload['gross_amount'] ?? $order->grand_total;

        if (!$transactionStatus) {
            return false;
        }

        $payment = Payment::firstOrNew(['order_id' => $order->id]);
        $payment->transaction_id = $transactionId ?? $payment->transaction_id;
        $payment->payment_type   = $paymentType ?? $payment->payment_type;
        $payment->gross_amount   = (float) $grossAmount;
        $payment->raw_payload    = $payload;

        $statusChanged = false;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $payment->transaction_status = 'settlement';
                $payment->paid_at            = now();
                $order->update([
                    'status'  => \App\Enums\OrderStatus::PAID,
                    'paid_at' => now(),
                ]);
                $statusChanged = true;
            } elseif ($fraudStatus === 'challenge') {
                $payment->transaction_status = 'pending';
            }
        } elseif ($transactionStatus === 'settlement') {
            $payment->transaction_status = 'settlement';
            $payment->paid_at            = now();
            $order->update([
                'status'  => \App\Enums\OrderStatus::PAID,
                'paid_at' => now(),
            ]);
            $statusChanged = true;
        } elseif ($transactionStatus === 'pending') {
            $payment->transaction_status = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $payment->transaction_status = 'cancel';
            if ($order->status !== \App\Enums\OrderStatus::CANCELLED) {
                $order->update([
                    'status'       => \App\Enums\OrderStatus::CANCELLED,
                    'cancelled_at' => now(),
                ]);
                app(\App\Services\InventoryService::class)->restoreStock($order, \App\Enums\StockReferenceType::RESTOCK_CANCELLED);
                $statusChanged = true;
            }
        } elseif ($transactionStatus === 'expire') {
            $payment->transaction_status = 'expire';
            if ($order->status !== \App\Enums\OrderStatus::EXPIRED) {
                $order->update([
                    'status'       => \App\Enums\OrderStatus::EXPIRED,
                    'cancelled_at' => now(),
                ]);
                app(\App\Services\InventoryService::class)->restoreStock($order, \App\Enums\StockReferenceType::RESTOCK_EXPIRED);
                $statusChanged = true;
            }
        }

        $payment->save();

        if ($statusChanged && $order->status === \App\Enums\OrderStatus::PAID) {
            try {
                app(\App\Services\WhatsAppService::class)->sendPaymentReceived($order);
            } catch (\Throwable $e) {
                Log::warning('WhatsApp Send Payment Received Failed during sync: ' . $e->getMessage());
            }
        }

        return $statusChanged;
    }

    /**
     * 5. Buat Snap Token untuk Pembayaran Langganan Ngizan Premium (Rp 100.000)
     */
    public function createSubscriptionSnapToken(\App\Models\User $user, \App\Models\PremiumSubscription $subscription): array
    {
        $payload = [
            'transaction_details' => [
                'order_id'     => $subscription->subscription_code,
                'gross_amount' => (int) $subscription->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone ?: '081234567890',
            ],
            'item_details' => [
                [
                    'id'       => 'NGIZAN-PREMIUM-1YR',
                    'price'    => (int) $subscription->amount,
                    'quantity' => 1,
                    'name'     => 'Langganan Ngizan Premium 1 Tahun (Diskon 5%)',
                ],
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit'       => 'hours',
                'duration'   => 2,
            ],
        ];

        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->asJson()
                ->timeout(20)
                ->post($this->snapApiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $snapToken = $data['token'] ?? null;
                $redirectUrl = $data['redirect_url'] ?? null;

                $subscription->update([
                    'snap_token'        => $snapToken,
                    'snap_redirect_url' => $redirectUrl,
                ]);

                return [
                    'snap_token'   => $snapToken,
                    'redirect_url' => $redirectUrl,
                ];
            }

        } catch (Exception $e) {
            Log::error('Midtrans Subscription Exception: ' . $e->getMessage(), ['code' => $subscription->subscription_code]);
            throw $e;
        }
    }

    /**
     * 6. Sinkronisasi Real-Time Status Langganan Ngizan Premium dari Midtrans API
     */
    public function syncSubscriptionStatus(\App\Models\PremiumSubscription $subscription): bool
    {
        $payload = $this->getTransactionStatus($subscription->subscription_code);
        if (!$payload) {
            return false;
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;

        if (!$transactionStatus) {
            return false;
        }

        $isSettled = ($transactionStatus === 'settlement') || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

        if ($isSettled) {
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
                        app(\App\Services\WhatsAppService::class)->sendMessage($user->phone, $msg);
                    } catch (\Throwable $e) {
                        Log::warning("Gagal kirim WA premium: " . $e->getMessage());
                    }
                }
            }

            return true;
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
            $subscription->update([
                'payment_status' => $transactionStatus,
            ]);
        }

        return false;
    }
}

