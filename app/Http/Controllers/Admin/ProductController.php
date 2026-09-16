<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JerseySize;
use App\Enums\JerseyType;
use App\Enums\StockReferenceType;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
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
        $query = Product::with(['category', 'variants']);

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
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan Produk Baru dengan Varian Matrix & WebP Optimization
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:200',
            'category_id'           => 'required|exists:categories,id',
            'base_price'            => 'required|numeric|min:0',
            'weight_grams'          => 'required|integer|min:50',
            'description'           => 'nullable|string',
            'allow_custom_nameset'  => 'nullable|boolean',
            'custom_nameset_price'  => 'nullable|numeric|min:0',
            'allow_patch'           => 'nullable|boolean',
            'patch_price'           => 'nullable|numeric|min:0',
            'available_patches'     => 'nullable|array',
            'thumbnail_front'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'thumbnail_back'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'variants'              => 'required|array|min:1',
            'variants.*.size'       => 'required|string|max:10',
            'variants.*.type'       => 'required|string|max:50',
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
                'name'                 => $validated['name'],
                'slug'                 => Str::slug($validated['name']) . '-' . rand(100, 999),
                'sku'                  => 'NGZ-' . strtoupper(Str::random(6)),
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

            // 3. Buat Matriks Varian Ukuran & Tipe
            foreach ($validated['variants'] as $v) {
                $variant = ProductVariant::create([
                    'product_id'       => $product->id,
                    'size'             => $v['size'],
                    'type'             => $v['type'],
                    'sku'              => $product->sku . '-' . strtoupper(substr($v['type'], 0, 1)) . '-' . $v['size'],
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
                ->with('success', "Produk jersey '{$product->name}' berhasil ditambahkan ke katalog!");

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
        $product->load(['category', 'variants']);
        $categories = Category::active()->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update Produk & Varian
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:200',
            'category_id'           => 'required|exists:categories,id',
            'base_price'            => 'required|numeric|min:0',
            'weight_grams'          => 'required|integer|min:50',
            'description'           => 'nullable|string',
            'allow_custom_nameset'  => 'nullable|boolean',
            'custom_nameset_price'  => 'nullable|numeric|min:0',
            'allow_patch'           => 'nullable|boolean',
            'patch_price'           => 'nullable|numeric|min:0',
            'available_patches'     => 'nullable|array',
            'thumbnail_front'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'thumbnail_back'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active'             => 'nullable|boolean',
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

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', "Katalog jersey '{$product->name}' berhasil diperbarui.");

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
