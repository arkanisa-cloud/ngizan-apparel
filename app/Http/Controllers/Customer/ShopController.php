<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

/**
 * Controller ShopController
 * Mengelola katalog toko jersey dan etalase beranda (Storefront Homepage)
 */
class ShopController extends Controller
{
    /**
     * 1. Homepage / Beranda Storefront Ngizan Apparel
     */
    public function home(): View
    {
        $categories = Category::active()->withCount('products')->get();

        $jerseyProducts = Product::active()
            ->whereHas('category', function ($q) {
                $q->where('slug', 'jersey')->orWhere('name', 'like', '%jersey%');
            })
            ->with(['category', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        $nonJerseyProducts = Product::active()
            ->whereHas('category', function ($q) {
                $q->where('slug', '!=', 'jersey')->where('name', 'not like', '%jersey%');
            })
            ->with(['category', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        // Fallback jika query jersey khusus kosong, ambil produk terbaru
        if ($jerseyProducts->isEmpty()) {
            $jerseyProducts = Product::active()->with(['category', 'variants'])->latest()->take(8)->get();
        }

        $featuredProducts = $jerseyProducts;

        $heroBanner = null;
        $promoBanner = null;

        if (Schema::hasTable('banners')) {
            $banners = Banner::all()->keyBy('key');
            $heroBanner = $banners->get('hero');
            $promoBanner = $banners->get('promo_banner');
        }

        return view('home', compact('categories', 'featuredProducts', 'jerseyProducts', 'nonJerseyProducts', 'heroBanner', 'promoBanner'));
    }

    /**
     * 2. Katalog Lengkap Produk & Filter
     */
    public function index(Request $request): View
    {
        $query = Product::active()->with(['category', 'variants']);

        // Filter berdasarkan kategori (slug atau ID)
        if ($request->filled('category')) {
            $categoryVal = $request->input('category');
            $query->whereHas('category', function ($q) use ($categoryVal) {
                $q->where('slug', $categoryVal)->orWhere('id', $categoryVal);
            });
        }

        // Pencarian teks nama produk / deskripsi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter varian ukuran
        if ($request->filled('size')) {
            $size = $request->input('size');
            $query->whereHas('variants', function ($q) use ($size) {
                $q->where('size', $size)->where('stock', '>', 0);
            });
        }

        // Filter tipe jersey
        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->whereHas('variants', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        // Pengurutan (Sorting)
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('products')->get();

        return view('customer.shop', compact('products', 'categories'));
    }

    /**
     * 3. Halaman Detail Produk & Live Nameset Studio
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'variants', 'reviews.user']);

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'variants'])
            ->take(4)
            ->get();

        return view('customer.product-detail', compact('product', 'relatedProducts'));
    }

    /**
     * 4. Halaman Kontak & Bantuan Storefront
     */
    public function contact(): View
    {
        return view('customer.contact');
    }
}
