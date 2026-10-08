<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\ImageOptimizationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * CategoryController
 * Controller untuk CRUD kategori produk dengan fitur upload gambar dan optimasi WebP
 */
class CategoryController extends Controller
{
    public function __construct(
        protected ImageOptimizationService $imageService
    ) {}

    /**
     * Display a listing of the resource.
     * Menampilkan semua kategori
     */
    public function index(): View
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     * Tampilkan form tambah kategori
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     * Simpan kategori baru beserta gambar jika ada (dikonversi ke WebP)
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Atur is_active dari status
        if ($request->has('status')) {
            $data['is_active'] = $request->input('status') === 'active';
        } else {
            $data['is_active'] = true;
        }

        // Upload & konversi gambar jika ada
        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->optimizeAndStore(
                $request->file('image'),
                'categories',
                85
            );
        }

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * Tampilkan detail kategori
     */
    public function show(Category $category): View
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     * Tampilkan form edit kategori
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * Update kategori beserta pergantian gambar ke WebP
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->has('status')) {
            $data['is_active'] = $request->input('status') === 'active';
        }

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($category->image) {
                $this->imageService->delete($category->image);
            }
            $data['image'] = $this->imageService->optimizeAndStore(
                $request->file('image'),
                'categories',
                85
            );
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     * Hapus kategori
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Cek apakah ada produk dalam kategori
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki produk.');
        }

        // Hapus file gambar jika ada
        if ($category->image) {
            $this->imageService->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
