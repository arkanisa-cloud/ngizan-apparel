<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\BiteshipService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncJntTrackingCommand extends Command
{
    /**
     * Nama dan signature command
     *
     * @var string
     */
    protected $signature = 'orders:sync-tracking';

    /**
     * Deskripsi command
     *
     * @var string
     */
    protected $description = 'Sinkronisasi status perjalanan paket J&T Express via Biteship dan auto-complete pesanan saat delivered';

    /**
     * Eksekusi command
     */
    public function handle(BiteshipService $biteshipService, WhatsAppService $whatsAppService): int
    {
        $this->info('Memulai sinkronisasi status pengiriman paket J&T...');

        $shippedOrders = Order::where('status', OrderStatus::SHIPPED)
            ->whereNotNull('tracking_number')
            ->get();

        if ($shippedOrders->isEmpty()) {
            $this->info('Tidak ada pesanan aktif berstatus shipped.');
            return Command::SUCCESS;
        }

        $completedCount = 0;

        foreach ($shippedOrders as $order) {
            try {
                $courier = $order->courier_code ?: 'jnt';
                $tracking = $biteshipService->getTracking($order->tracking_number, $courier);

                if (!empty($tracking['success'])) {
                    $status = strtolower($tracking['status'] ?? '');

                    // Cek jika kurir menyatakan paket telah tiba / selesai
                    if (in_array($status, ['delivered', 'selesai', 'sukses'])) {
                        $order->update([
                            'status'       => OrderStatus::COMPLETED,
                            'completed_at' => now(),
                        ]);

                        $completedCount++;
                        $this->info("Pesanan #{$order->order_number} telah sampai -> Status diubah ke COMPLETED.");

                        // Kirim WhatsApp notifikasi paket tiba & ajak review jika nomor WA ada
                        if ($order->customer_phone) {
                            try {
                                $reviewUrl = route('customer.orders.show', $order->order_number);
                                $msg = "Halo *{$order->customer_name}*! Paket jersey pesanan Anda #{$order->order_number} telah tiba di alamat tujuan via J&T Express.\n\n"
                                    . "Bagaimana kualitas jerseynya? Berikan rating bintang (1-5) & ulasan Anda di sini:\n"
                                    . $reviewUrl . "\n\nTerima kasih telah berbelanja di Ngizan Apparel! ⚽";

                                $whatsAppService->sendMessage($order->customer_phone, $msg);
                            } catch (\Throwable $e) {
                                Log::warning("Gagal mengirim WA paket tiba untuk #{$order->order_number}: " . $e->getMessage());
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Gagal sinkronisasi tracking pesanan #{$order->order_number}: " . $e->getMessage());
                $this->error("Gagal tracking #{$order->order_number}: " . $e->getMessage());
            }
        }

        $this->info("Sinkronisasi selesai. {$completedCount} pesanan telah otomatis diselesaikan.");
        return Command::SUCCESS;
    }
}
