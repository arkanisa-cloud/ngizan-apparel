<?php

namespace App\Services;

use App\Enums\StockReferenceType;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service InventoryService
 * Mengelola reservasi stok anti-overselling (Pessimistic Locking / lockForUpdate)
 * dan pengembalian stok saat pesanan expired atau dibatalkan, beserta audit log StockHistory.
 */
class InventoryService
{
    /**
     * 1. Reservasi / Pengurangan Stok saat Checkout (Anti-Overselling Guard)
     *
     * @param Collection|array $cartItems Item-item keranjang yang dipesan
     * @param Order $order Model pesanan yang baru dibuat
     * @throws Exception Jika stok tidak mencukupi
     */
    public function reserveStock(Collection|array $cartItems, Order $order): void
    {
        DB::transaction(function () use ($cartItems, $order) {
            foreach ($cartItems as $item) {
                $variantId = is_array($item) ? $item['product_variant_id'] : $item->product_variant_id;
                $quantity  = is_array($item) ? $item['quantity'] : $item->quantity;

                // Kunci baris data varian untuk mencegah race condition (Overselling)
                $variant = ProductVariant::where('id', $variantId)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    throw new Exception("Varian produk dengan ID #{$variantId} tidak ditemukan.");
                }

                if ($variant->stock < $quantity) {
                    $productName = $variant->product ? $variant->product->name : 'Jersey';
                    throw new Exception("Stok untuk {$productName} (Ukuran {$variant->size}) tidak mencukupi. Sisa stok: {$variant->stock}.");
                }

                $stockBefore = $variant->stock;
                $stockAfter  = $stockBefore - $quantity;

                // Kurangi stok
                $variant->update(['stock' => $stockAfter]);

                // Catat mutasi kartu stok
                StockHistory::create([
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'user_id'            => $order->user_id,
                    'reference_type'     => StockReferenceType::ORDER_PLACED,
                    'reference_id'       => $order->order_number,
                    'quantity_change'    => -$quantity,
                    'stock_before'       => $stockBefore,
                    'stock_after'        => $stockAfter,
                    'notes'              => "Pengurangan stok otomatis pesanan #{$order->order_number}",
                ]);
            }
        });
    }

    /**
     * 2. Pengembalian Stok ke Katalog saat Pesanan Dibatalkan / Kadaluarsa
     *
     * @param Order $order
     * @param StockReferenceType $reason (RESTOCK_EXPIRED atau RESTOCK_CANCELLED)
     */
    public function restoreStock(Order $order, StockReferenceType $reason = StockReferenceType::RESTOCK_EXPIRED): void
    {
        DB::transaction(function () use ($order, $reason) {
            foreach ($order->items as $item) {
                if (!$item->product_variant_id) {
                    continue;
                }

                $variant = ProductVariant::where('id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (!$variant) {
                    continue;
                }

                $stockBefore = $variant->stock;
                $stockAfter  = $stockBefore + $item->quantity;

                // Kembalikan stok
                $variant->update(['stock' => $stockAfter]);

                // Catat mutasi pengembalian stok
                StockHistory::create([
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'user_id'            => $order->user_id,
                    'reference_type'     => $reason,
                    'reference_id'       => $order->order_number,
                    'quantity_change'    => $item->quantity,
                    'stock_before'       => $stockBefore,
                    'stock_after'        => $stockAfter,
                    'notes'              => "Pengembalian stok pesanan #{$order->order_number} ({$reason->label()})",
                ]);
            }

            Log::info("Stok untuk pesanan #{$order->order_number} berhasil dikembalikan ke katalog ({$reason->value}).");
        });
    }
}
