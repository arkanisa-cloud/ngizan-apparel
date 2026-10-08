<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageOptimizationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * BannerController
 * Mengelola gambar Hero Section dan Promo Banner dengan optimasi WebP
 */
class BannerController extends Controller
{
    public function __construct(
        protected ImageOptimizationService $imageService
    ) {}

    /**
     * Tampilkan halaman kelola gambar Hero & Banner
     */
    public function index(): View
    {
        $heroBanner = Banner::firstOrCreate(
            ['key' => 'hero']
        );

        $promoBanner = Banner::firstOrCreate(
            ['key' => 'promo_banner']
        );

        return view('admin.banners.index', compact('heroBanner', 'promoBanner'));
    }

    /**
     * Update gambar banner dengan konversi WebP otomatis
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
        ], [
            'image.required' => 'Silakan pilih file gambar yang ingin diunggah.',
            'image.image'    => 'File harus berupa gambar.',
            'image.mimes'    => 'Format gambar harus jpeg, png, jpg, webp, svg, atau gif.',
            'image.max'      => 'Ukuran gambar maksimal 10MB.',
        ]);

        if ($request->hasFile('image')) {
            // Hapus file gambar lama jika ada
            if ($banner->image) {
                $this->imageService->delete($banner->image);
            }

            // Konversi dan simpan gambar sebagai format .webp
            $path = $this->imageService->optimizeAndStore(
                $request->file('image'),
                'banners',
                85
            );

            $banner->image = $path;
            $banner->save();
        }

        $label = $banner->key === 'hero' ? 'Hero Section' : 'Promo Banner';

        return redirect()
            ->route('admin.banners.index')
            ->with('success', "Gambar {$label} berhasil diperbarui dan dikonversi ke format WebP.");
    }
}
