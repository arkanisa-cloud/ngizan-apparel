<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JerseySize;
use App\Enums\JerseyType;
use App\Enums\StockReferenceType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
use App\Models\StockHistory;
use App\Services\ImageOptimizationService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Controller ProductController (Admin)
 * Mengelola Master Jersey, Matrix Varian Ukuran/Tipe, dan Konversi Gambar WebP Otomatis
 */
class ProductController extends Controller
{
    public function __construct(
        protected ImageOptimizationService $imageService
    ) {}

    /**
     * List Katalog Produk
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'variants', 'sizeChart']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form Tambah Produk Baru
     */
    public function create(): View
    {
        $categories = Category::active()->get();
        $sizeCharts = SizeChart::orderBy('is_default', 'desc')->get();
        return view('admin.products.create', compact('categories', 'sizeCharts'));
    }

    /**
     * Simpan Produk Baru dengan Varian Matrix & WebP Optimization
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:200',
            'category_id'           => 'required|exists:categories,id',
            'size_chart_id'         => 'nullable|exists:size_charts,id',
            'base_price'            => 'required|numeric|min:0',
            'weight_grams'          => 'required|integer|min:50',
            'description'           => 'nullable|string',
            'allow_custom_nameset'  => 'nullable|boolean',
            'custom_nameset_price'  => 'nullable|numeric|min:0',
            'allow_patch'           => 'nullable|boolean',
            'patch_price'           => 'nullable|numeric|min:0',
            'available_patches'     => 'nullable|array',
            'thumbnail_front'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'thumbnail_back'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'variants'              => 'required|array|min:1',
            'variants.*.size'       => 'required|string|max:50',
            'variants.*.type'       => 'nullable|string|max:50',
            'variants.*.stock'      => 'required|integer|min:0',
            'variants.*.price_adj'  => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Upload & Konversi WebP Foto Depan & Belakang
            $frontPath = null;
            $backPath  = null;

            if ($request->hasFile('thumbnail_front')) {
                $frontPath = $this->imageService->optimizeAndStore(
                    $request->file('thumbnail_front'),
                    'products/front'
                );
            }

            if ($request->hasFile('thumbnail_back')) {
                $backPath = $this->imageService->optimizeAndStore(
                    $request->file('thumbnail_back'),
                    'products/back'
                );
            }

            // 2. Buat Record Product
            $product = Product::create([
                'category_id'          => $validated['category_id'],
                'size_chart_id'        => $validated['size_chart_id'] ?? null,
                'name'                 => $validated['name'],
                'slug'                 => Str::slug($validated['name']) . '-' . rand(100, 999),
                'base_price'           => $validated['base_price'],
                'weight_grams'         => $validated['weight_grams'],
                'description'          => $validated['description'] ?? null,
                'thumbnail_front'      => $frontPath,
                'thumbnail_back'       => $backPath,
                'allow_custom_nameset' => !empty($validated['allow_custom_nameset']),
                'custom_nameset_price' => $validated['custom_nameset_price'] ?? 50000,
                'allow_patch'          => !empty($validated['allow_patch']),
                'patch_price'          => $validated['patch_price'] ?? 35000,
                'available_patches'    => $validated['available_patches'] ?? ['UCL Starball', 'Premier League Gold', 'FIFA World Cup'],
                'is_active'            => true,
            ]);

            // 3. Buat Matriks Varian Ukuran dengan SKU Unik
            $baseSku = 'NGZ-' . strtoupper(Str::random(6));
            foreach ($validated['variants'] as $v) {
                $typeVal = !empty(trim($v['type'] ?? '')) ? trim($v['type']) : 'Standard';
                $sizeCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $v['size'])) ?: 'STD';
                $variantSku = $baseSku . '-' . $sizeCode;
                
                // Pastikan SKU benar-benar unik di tabel product_variants
                while (ProductVariant::where('sku', $variantSku)->exists()) {
                    $variantSku = 'NGZ-' . strtoupper(Str::random(6)) . '-' . $sizeCode;
                }

                $variant = ProductVariant::create([
                    'product_id'       => $product->id,
                    'size'             => trim($v['size']),
                    'type'             => $typeVal,
                    'sku'              => $variantSku,
                    'price_adjustment' => $v['price_adj'] ?? 0,
                    'stock'            => (int) $v['stock'],
                ]);

                // Catat di StockHistory jika stok awal > 0
                if ($variant->stock > 0) {
                    StockHistory::create([
                        'product_id'         => $product->id,
                        'product_variant_id' => $variant->id,
                        'reference_type'     => StockReferenceType::MANUAL_IN,
                        'quantity_change'    => $variant->stock,
                        'stock_before'       => 0,
                        'stock_after'        => $variant->stock,
                        'notes'              => 'Stok Awal Produk Baru',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', "Produk '{$product->name}' berhasil ditambahkan ke katalog!");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Detail Produk & Riwayat Stok
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'variants.stockHistories' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Form Edit Produk
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'variants', 'sizeChart']);
        $categories = Category::active()->get();
        $sizeCharts = SizeChart::orderBy('is_default', 'desc')->get();

        return view('admin.products.edit', compact('product', 'categories', 'sizeCharts'));
    }

    /**
     * Update Produk & Varian
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:200',
            'category_id'           => 'required|exists:categories,id',
            'size_chart_id'         => 'nullable|exists:size_charts,id',
            'base_price'            => 'required|numeric|min:0',
            'weight_grams'          => 'required|integer|min:50',
            'description'           => 'nullable|string',
            'allow_custom_nameset'  => 'nullable|boolean',
            'custom_nameset_price'  => 'nullable|numeric|min:0',
            'allow_patch'           => 'nullable|boolean',
            'patch_price'           => 'nullable|numeric|min:0',
            'available_patches'     => 'nullable|array',
            'thumbnail_front'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'thumbnail_back'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'is_active'             => 'nullable|boolean',
            'variants'              => 'nullable|array|min:1',
            'variants.*.id'         => 'nullable|integer|exists:product_variants,id',
            'variants.*.size'       => 'required_with:variants|string|max:50',
            'variants.*.type'       => 'nullable|string|max:50',
            'variants.*.stock'      => 'required_with:variants|integer|min:0',
            'variants.*.price_adj'  => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update Gambar jika ada upload baru
            if ($request->hasFile('thumbnail_front')) {
                if ($product->thumbnail_front) $this->imageService->delete($product->thumbnail_front);
                $validated['thumbnail_front'] = $this->imageService->optimizeAndStore($request->file('thumbnail_front'), 'products/front');
            }

            if ($request->hasFile('thumbnail_back')) {
                if ($product->thumbnail_back) $this->imageService->delete($product->thumbnail_back);
                $validated['thumbnail_back'] = $this->imageService->optimizeAndStore($request->file('thumbnail_back'), 'products/back');
            }

            $validated['allow_custom_nameset'] = !empty($validated['allow_custom_nameset']);
            $validated['allow_patch']          = !empty($validated['allow_patch']);
            $validated['is_active']            = !empty($validated['is_active']);

            $product->update($validated);

            // Sinkronisasi Varian Ukuran & Stok
            if (isset($validated['variants']) && is_array($validated['variants'])) {
                $submittedVariantIds = [];
                $baseSku = 'NGZ-' . strtoupper(Str::random(6));

                foreach ($validated['variants'] as $v) {
                    $priceAdj = isset($v['price_adj']) ? (float)$v['price_adj'] : 0;
                    $stockVal = (int)($v['stock'] ?? 0);

                    if (!empty($v['id'])) {
                        // Update existing variant
                        $variant = ProductVariant::where('product_id', $product->id)->find($v['id']);
                        if ($variant) {
                            $stockDiff = $stockVal - $variant->stock;
                            $oldStock = $variant->stock;

                            $variant->update([
                                'size'             => trim($v['size']),
                                'price_adjustment' => $priceAdj,
                                'stock'            => $stockVal,
                            ]);

                            if ($stockDiff != 0) {
                                StockHistory::create([
                                    'product_id'         => $product->id,
                                    'product_variant_id' => $variant->id,
                                    'reference_type'     => StockReferenceType::MANUAL_ADJUST,
                                    'quantity_change'    => $stockDiff,
                                    'stock_before'       => $oldStock,
                                    'stock_after'        => $stockVal,
                                    'notes'              => 'Penyesuaian stok via edit katalog produk',
                                ]);
                            }

                            $submittedVariantIds[] = $variant->id;
                        }
                    } else {
                        // Create new variant
                        $sizeCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $v['size'])) ?: 'STD';
                        $variantSku = $baseSku . '-' . $sizeCode;
                        while (ProductVariant::where('sku', $variantSku)->exists()) {
                            $variantSku = 'NGZ-' . strtoupper(Str::random(6)) . '-' . $sizeCode;
                        }

                        $newVariant = ProductVariant::create([
                            'product_id'       => $product->id,
                            'size'             => trim($v['size']),
                            'type'             => 'Standard',
                            'sku'              => $variantSku,
                            'price_adjustment' => $priceAdj,
                            'stock'            => $stockVal,
                        ]);

                        if ($newVariant->stock > 0) {
                            StockHistory::create([
                                'product_id'         => $product->id,
                                'product_variant_id' => $newVariant->id,
                                'reference_type'     => StockReferenceType::MANUAL_IN,
                                'quantity_change'    => $newVariant->stock,
                                'stock_before'       => 0,
                                'stock_after'        => $newVariant->stock,
                                'notes'              => 'Stok varian baru via edit katalog',
                            ]);
                        }

                        $submittedVariantIds[] = $newVariant->id;
                    }
                }

                // Delete variants removed by admin
                $variantsToDelete = ProductVariant::where('product_id', $product->id)
                    ->whereNotIn('id', $submittedVariantIds)
                    ->get();

                foreach ($variantsToDelete as $delVariant) {
                    $delVariant->delete();
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', "Katalog produk '{$product->name}' berhasil diperbarui.");

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Produk
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->thumbnail_front) $this->imageService->delete($product->thumbnail_front);
        if ($product->thumbnail_back)  $this->imageService->delete($product->thumbnail_back);

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus dari katalog.');
    }
}
