<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller CartController
 * Mengelola keranjang belanja pelanggan dengan dukungan kustomisasi nameset, patch, dan kuantitas
 */
class CartController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja
     */
    public function index(): View
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $cart->load(['items.product', 'items.variant']);

        return view('customer.cart', compact('cart'));
    }

    /**
     * Tambahkan item jersey ke keranjang belanja
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id'         => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity'           => 'nullable|integer|min:1|max:20',
            'custom_name'        => 'nullable|string|max:12',
            'custom_number'      => 'nullable|string|max:2',
            'selected_patch'     => 'nullable|string|max:100',
        ]);

        $qty = (int) ($validated['quantity'] ?? 1);
        $variant = ProductVariant::with('product')->findOrFail($validated['product_variant_id']);
        $product = $variant->product;

        // Validasi stok
        if ($variant->stock < $qty) {
            $msg = "Stok {$product->name} ukuran {$variant->size} tidak mencukupi (Sisa: {$variant->stock}).";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg)->withInput();
        }

        // Kalkulasi biaya kustom
        $hasNameset = $product->allow_custom_nameset && (!empty($validated['custom_name']) || !empty($validated['custom_number']));
        $hasPatch = $product->allow_patch && !empty($validated['selected_patch']);

        $unitPrice = $variant->final_price;
        $customFee = ($hasNameset ? (float)$product->custom_nameset_price : 0) + ($hasPatch ? (float)$product->patch_price : 0);
        $totalPrice = ($unitPrice + $customFee) * $qty;

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        // Cek apakah ada item dengan konfigurasi kustomisasi yang persis sama
        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->where('custom_name', $validated['custom_name'] ?? null)
            ->where('custom_number', $validated['custom_number'] ?? null)
            ->where('selected_patch', $validated['selected_patch'] ?? null)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $qty;
            if ($variant->stock < $newQty) {
                $msg = "Total kuantitas ({$newQty}) melebihi stok tersedia ({$variant->stock}).";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg)->withInput();
            }

            $existingItem->update([
                'quantity'    => $newQty,
                'total_price' => ($unitPrice + $customFee) * $newQty,
            ]);
        } else {
            CartItem::create([
                'cart_id'            => $cart->id,
                'product_id'         => $product->id,
                'product_variant_id' => $variant->id,
                'quantity'           => $qty,
                'custom_name'        => $validated['custom_name'] ?? null,
                'custom_number'      => $validated['custom_number'] ?? null,
                'selected_patch'     => $validated['selected_patch'] ?? null,
                'unit_price'         => $unitPrice,
                'custom_fee'         => $customFee,
                'total_price'        => $totalPrice,
            ]);
        }

        $successMsg = "Jersey {$product->name} ({$variant->size}) berhasil ditambahkan ke tas belanja!";

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => $successMsg,
                'cart_count' => $cart->items()->sum('quantity'),
            ]);
        }

        return redirect()->route('customer.cart.index')->with('success', $successMsg);
    }

    /**
     * Perbarui jumlah item di keranjang
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:20',
        ]);

        $newQty = (int) $validated['quantity'];
        $variant = $cartItem->variant;

        if ($variant && $variant->stock < $newQty) {
            $msg = "Stok tidak mencukupi! Sisa stok tersedia: {$variant->stock}.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $totalPrice = ($cartItem->unit_price + $cartItem->custom_fee) * $newQty;
        $cartItem->update([
            'quantity'    => $newQty,
            'total_price' => $totalPrice,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Kuantitas keranjang diperbarui.',
                'item_total'  => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
                'cart_total'  => $cartItem->cart->formatted_total_price,
            ]);
        }

        return back()->with('info', 'Keranjang belanja diperbarui.');
    }

    /**
     * Hapus satu item dari keranjang
     */
    public function destroy(CartItem $cartItem): RedirectResponse|JsonResponse
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Item dihapus dari keranjang.',
                'cart_count' => $cart->items()->sum('quantity'),
                'cart_total' => $cart->formatted_total_price,
            ]);
        }

        return back()->with('info', 'Item dihapus dari tas belanja.');
    }

    /**
     * Kosongkan seluruh isi keranjang
     */
    public function clear(): RedirectResponse
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('info', 'Keranjang belanja telah dikosongkan.');
    }
}
