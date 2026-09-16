<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service WhatsAppService
 * Mengirimkan pesan notifikasi transaksi otomatis ke nomor WhatsApp pelanggan via Fonnte Gateway API
 */
class WhatsAppService
{
    protected string $apiToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiToken = (string) config('services.fonnte.token');
        $this->apiUrl   = (string) config('services.fonnte.api_url', 'https://api.fonnte.com/send');
    }

    /**
     * Kirim pesan teks WhatsApp generik
     *
     * @param string $targetPhone Nomor WhatsApp tujuan
     * @param string $message Teks pesan format markdown WhatsApp
     * @return array Response payload
     */
    public function sendMessage(string $targetPhone, string $message): array
    {
        $cleanPhone = $this->formatPhoneNumber($targetPhone);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])
                ->asForm()
                ->timeout(15)
                ->post($this->apiUrl, [
                    'target'  => $cleanPhone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                return $response->json() ?? ['status' => true];
            }

            Log::warning("Fonnte WA API Warning: " . $response->body());
            return [
                'status'  => false,
                'message' => $response->body(),
            ];
        } catch (Exception $e) {
            Log::error("Fonnte WA API Exception: " . $e->getMessage());
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Notifikasi 1: Pesanan Baru Dibuat (Menunggu Pembayaran)
     */
    public function sendOrderCreated(Order $order): array
    {
        $orderLink = url("/customer/orders/{$order->id}");
        $message = "Halo *{$order->customer_name}*! 👋\n\n"
            . "Terima kasih telah memesan di *Ngizan Apparel*.\n"
            . "Pesanan Anda dengan nomor *#{$order->order_number}* telah berhasil dicatat.\n\n"
            . "💰 *Total Tagihan*: {$order->formatted_grand_total}\n"
            . "⏳ *Batas Waktu Pembayaran*: 2 Jam\n\n"
            . "Silakan selesaikan pembayaran Anda melalui tautan berikut:\n"
            . "👉 {$orderLink}\n\n"
            . "_Ngizan Apparel - Bespoke Football Kits & Archive Store_";

        return $this->sendMessage($order->customer_phone, $message);
    }

    /**
     * Notifikasi 2: Pembayaran Lunas Terverifikasi
     */
    public function sendPaymentReceived(Order $order): array
    {
        $orderLink = url("/customer/orders/{$order->id}");
        $sablonNotice = $order->hasCustomSablon()
            ? "\n🧵 *Catatan*: Jersey Anda memiliki kustomisasi sablon/nameset dan sedang masuk ke antrean mesin press bagian produksi."
            : "";

        $message = "Halo *{$order->customer_name}*! ✅\n\n"
            . "Pembayaran untuk pesanan *#{$order->order_number}* sebesar *{$order->formatted_grand_total}* telah *LUNAS TERVERIFIKASI*."
            . $sablonNotice . "\n\n"
            . "Kami akan segera memproses dan mengemas jersey Anda dengan aman.\n\n"
            . "Pantau status pesanan Anda di sini:\n"
            . "👉 {$orderLink}\n\n"
            . "_Terima kasih atas kepercayaan Anda pada Ngizan Apparel!_";

        return $this->sendMessage($order->customer_phone, $message);
    }

    /**
     * Notifikasi 3: Pesanan Dikirim Bersama Nomor Resi
     */
    public function sendOrderShipped(Order $order): array
    {
        $orderLink = url("/customer/orders/{$order->id}");
        $courierName = strtoupper($order->courier_code ?? 'Ekspedisi');
        $serviceName = $order->courier_service_name ?? 'Reguler';
        $trackingNo = $order->tracking_number ?? '-';

        $message = "Halo *{$order->customer_name}*! 🚚📦\n\n"
            . "Kabar gembira! Pesanan jersey Anda *#{$order->order_number}* telah diserahkan ke kurir dan sedang dalam perjalanan.\n\n"
            . "🚛 *Kurir*: {$courierName} ({$serviceName})\n"
            . "🔖 *Nomor Resi / Waybill*: *{$trackingNo}*\n\n"
            . "Lacak perjalanan paket Anda secara langsung di:\n"
            . "👉 {$orderLink}\n\n"
            . "_Selamat menunggu jersey baru Anda tiba di rumah! ✨_";

        return $this->sendMessage($order->customer_phone, $message);
    }

    /**
     * Normalisasi format nomor telepon Indonesia (misal: 0812... -> 62812...)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '8')) {
            return '62' . $cleaned;
        }

        return $cleaned;
    }
}
