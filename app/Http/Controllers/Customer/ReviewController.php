<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan bintang dan komentar pelanggan (Verified Buyer Guard)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|min:3|max:1000',
        ]);

        $order = Order::with('items')->findOrFail($validated['order_id']);

        // 1. Verifikasi Kepemilikan Order (Anti-IDOR)
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengulas pesanan ini.');
        }

        // 2. Verifikasi Status Pesanan Harus COMPLETED (Verified Buyer)
        if ($order->status !== OrderStatus::COMPLETED) {
            abort(403, 'Ulasan hanya dapat diberikan setelah pesanan Anda telah selesai/diterima.');
        }

        // 3. Verifikasi Produk Termasuk dalam Pesanan Ini
        $hasProduct = $order->items->contains('product_id', $validated['product_id']);
        if (!$hasProduct) {
            abort(403, 'Produk ini tidak terdapat dalam pesanan yang bersangkutan.');
        }

        // 4. Verifikasi Belum Pernah Memberi Ulasan untuk Item Pesanan Ini
        $existing = Review::where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->where('order_id', $validated['order_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini pada pesanan ini.');
        }

        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $validated['product_id'],
            'order_id'   => $validated['order_id'],
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'],
        ]);

        return back()->with('success', 'Terima kasih! Ulasan dan rating bintang Anda berhasil diterbitkan.');
    }
}
