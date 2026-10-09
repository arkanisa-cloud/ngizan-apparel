<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockReferenceType;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\StockIn;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller StockInController (Admin)
 * Mengelola pencatatan stok masuk restock dan audit trail inventori
 */
class StockInController extends Controller
{
    public function index(): View
    {
        $stockIns = StockIn::with(['variant.product'])
            ->latest()
            ->paginate(12);

        return view('admin.stock-ins.index', compact('stockIns'));
    }

    public function create(): View
    {
        $products = Product::active()->with('variants')->get();

        return view('admin.stock-ins.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity'           => 'required|integer|min:1',
            'purchase_price'     => 'required|numeric|min:0',
            'invoice_number'     => 'nullable|string|max:100',
            'received_date'      => 'required|date',
            'notes'              => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($validated['product_variant_id']);
            $stockBefore = $variant->stock;
            $stockIn = StockIn::create($validated);

            $variant->increment('stock', $validated['quantity']);

            StockHistory::create([
                'product_id'         => $variant->product_id,
                'product_variant_id' => $variant->id,
                'reference_type'     => StockReferenceType::MANUAL_IN,
                'reference_id'       => (string) $stockIn->id,
                'quantity_change'    => $validated['quantity'],
                'stock_before'       => $stockBefore,
                'stock_after'        => $variant->stock,
                'notes'              => 'Restock Invoice: ' . ($validated['invoice_number'] ?: '-'),
            ]);

            DB::commit();
            return redirect()->route('admin.stock-ins.index')
                ->with('success', "Stok masuk {$validated['quantity']} pcs untuk jersey {$variant->product->name} ({$variant->size}) berhasil ditambahkan.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses stok masuk: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockIn $stockIn): View
    {
        $stockIn->load(['variant.product']);
        return view('admin.stock-ins.show', compact('stockIn'));
    }
}
