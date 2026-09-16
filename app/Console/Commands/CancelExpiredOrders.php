<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\StockReferenceType;
use App\Models\Order;
use App\Services\InventoryService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan pesanan yang belum dibayar dalam batas waktu 2 jam dan pulihkan stok jersey.';

    /**
     * Execute the console command.
     */
    public function handle(InventoryService $inventory): int
    {
        $this->info('Mencari pesanan kedaluwarsa...');

        $expiredOrders = Order::where('status', OrderStatus::PENDING_PAYMENT)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->with('items')
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('Tidak ada pesanan kedaluwarsa.');
            return 0;
        }

        $count = 0;
        foreach ($expiredOrders as $order) {
            DB::beginTransaction();
            try {
                $order->update([
                    'status'       => OrderStatus::EXPIRED,
                    'cancelled_at' => now(),
                ]);

                if ($order->payment) {
                    $order->payment->update(['status' => PaymentStatus::EXPIRE]);
                }

                // Kembalikan stok varian jersey
                $inventory->restoreStock($order, StockReferenceType::RESTOCK_EXPIRED);

                DB::commit();
                $count++;
                $this->line("Pesanan #{$order->order_number} berhasil dibatalkan dan stok dikembalikan.");
            } catch (Exception $e) {
                DB::rollBack();
                $this->error("Gagal memproses pesanan #{$order->order_number}: " . $e->getMessage());
                Log::error("CancelExpiredOrders Error (#{$order->order_number}): " . $e->getMessage());
            }
        }

        $this->info("Selesai. Total {$count} pesanan kedaluwarsa berhasil dibatalkan.");
        return 0;
    }
}
