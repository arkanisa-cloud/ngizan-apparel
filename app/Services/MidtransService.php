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
     * 3. Buat Snap Token untuk Pembayaran Langganan Ngizan Premium (Rp 100.000)
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

            Log::error('Midtrans Subscription Snap Error: ' . $response->body(), ['code' => $subscription->subscription_code]);
            throw new Exception($response->json('error_messages.0') ?? 'Gagal membuat Snap Token langganan.');
        } catch (Exception $e) {
            Log::error('Midtrans Subscription Exception: ' . $e->getMessage(), ['code' => $subscription->subscription_code]);
            throw $e;
        }
    }
}
