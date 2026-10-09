<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeChart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controller SizeChartController
 * Mengelola master template panduan ukuran untuk berbagai kategori pakaian (Jersey, Celana, Trackpants, dll)
 */
class SizeChartController extends Controller
{
    /**
     * Tampilkan daftar template panduan ukuran
     */
    public function index(): View
    {
        $sizeCharts = SizeChart::withCount('products')
            ->orderBy('is_default', 'desc')
            ->latest()
            ->paginate(15);

        return view('admin.size-charts.index', compact('sizeCharts'));
    }

    /**
     * Form tambah template panduan ukuran baru
     */
    public function create(): View
    {
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.size-charts.create', compact('categories'));
    }

    /**
     * Simpan template panduan ukuran baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150|unique:size_charts,name',
            'category_type' => 'required|string|max:100',
            'description'   => 'nullable|string|max:500',
            'columns'       => 'required|array|min:2',
            'columns.*'     => 'required|string|max:100',
            'rows'          => 'required|array|min:1',
            'rows.*.size'   => 'required|string|max:50',
            'is_default'    => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            SizeChart::query()->update(['is_default' => false]);
        }

        SizeChart::create([
            'name'          => $validated['name'],
            'category_type' => $validated['category_type'],
            'description'   => $validated['description'] ?? null,
            'columns'       => array_values($validated['columns']),
            'rows'          => array_values($validated['rows']),
            'is_default'    => !empty($validated['is_default']),
        ]);

        return redirect()->route('admin.size-charts.index')
            ->with('success', "Template Panduan Ukuran '{$validated['name']}' berhasil ditambahkan!");
    }

    /**
     * Form edit template panduan ukuran
     */
    public function edit(SizeChart $sizeChart): View
    {
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.size-charts.edit', compact('sizeChart', 'categories'));
    }

    /**
     * Perbarui data template panduan ukuran
     */
    public function update(Request $request, SizeChart $sizeChart): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150|unique:size_charts,name,' . $sizeChart->id,
            'category_type' => 'required|string|max:100',
            'description'   => 'nullable|string|max:500',
            'columns'       => 'required|array|min:2',
            'columns.*'     => 'required|string|max:100',
            'rows'          => 'required|array|min:1',
            'rows.*.size'   => 'required|string|max:50',
            'is_default'    => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            SizeChart::where('id', '!=', $sizeChart->id)->update(['is_default' => false]);
        }

        $sizeChart->update([
            'name'          => $validated['name'],
            'category_type' => $validated['category_type'],
            'description'   => $validated['description'] ?? null,
            'columns'       => array_values($validated['columns']),
            'rows'          => array_values($validated['rows']),
            'is_default'    => !empty($validated['is_default']),
        ]);

        return redirect()->route('admin.size-charts.index')
            ->with('success', "Template Panduan Ukuran '{$sizeChart->name}' berhasil diperbarui!");
    }

    /**
     * Hapus template panduan ukuran
     */
    public function destroy(SizeChart $sizeChart): RedirectResponse
    {
        if ($sizeChart->products()->exists()) {
            // Re-assign produk ke default size chart sebelum dihapus
            $default = SizeChart::where('is_default', true)->where('id', '!=', $sizeChart->id)->first()
                ?? SizeChart::where('id', '!=', $sizeChart->id)->first();

            $sizeChart->products()->update(['size_chart_id' => $default?->id]);
        }

        $name = $sizeChart->name;
        $sizeChart->delete();

        return redirect()->route('admin.size-charts.index')
            ->with('success', "Template Panduan Ukuran '{$name}' telah dihapus.");
    }
}
