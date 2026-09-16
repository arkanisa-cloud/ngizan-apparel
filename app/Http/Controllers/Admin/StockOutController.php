<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockReferenceType;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\StockOut;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller StockOutController (Admin)
 * Mengelola pencatatan stok keluar non-penjualan (rusak, display sampel, promosi)
 */
class StockOutController extends Controller
{
    public function index(): View
    {
        $stockOuts = StockOut::with(['variant.product'])
            ->latest()
            ->paginate(12);

        return view('admin.stock-outs.index', compact('stockOuts'));
    }

    public function create(): View
    {
        $products = Product::active()->with('variants')->get();
        return view('admin.stock-outs.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1',
            'reason'             => 'required|string|in:Damaged,Sample,Promotion,Loss,Expired',
            'out_date'           => 'required|date',
            'notes'              => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($validated['product_variant_id']);

            if ($variant->stock < $validated['quantity']) {
                throw new Exception("Stok tidak mencukupi! Sisa stok fisik varian {$variant->size}: {$variant->stock} pcs.");
            }

            $stockBefore = $variant->stock;
            $stockOut = StockOut::create($validated);

            $variant->decrement('stock', $validated['quantity']);

            StockHistory::create([
                'product_id'         => $variant->product_id,
                'product_variant_id' => $variant->id,
                'reference_type'     => StockReferenceType::MANUAL_OUT,
                'reference_id'       => (string) $stockOut->id,
                'quantity_change'    => -$validated['quantity'],
                'stock_before'       => $stockBefore,
                'stock_after'        => $variant->stock,
                'notes'              => "Stok Keluar ({$validated['reason']}): " . ($validated['notes'] ?: '-'),
            ]);

            DB::commit();
            return redirect()->route('admin.stock-outs.index')
                ->with('success', "Penyesuaian stok keluar {$validated['quantity']} pcs jersey {$variant->product->name} ({$variant->size}) berhasil disimpan.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses stok keluar: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockOut $stockOut): View
    {
        $stockOut->load(['variant.product']);
        return view('admin.stock-outs.show', compact('stockOut'));
    }
}
