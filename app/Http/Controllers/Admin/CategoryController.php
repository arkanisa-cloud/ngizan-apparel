<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * CategoryController
 * Controller untuk CRUD kategori produk
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan semua kategori
     */
    public function index(): View
    {
        $categories = Category::all();
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
     * Simpan kategori baru
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        // Simpan kategori menggunakan data yang sudah divalidasi oleh CategoryRequest
        Category::create($request->validated());

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
        // Load produk dalam kategori
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
     * Update kategori
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        // Update kategori menggunakan data yang sudah divalidasi oleh CategoryRequest
        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
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

        // Hapus kategori
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
