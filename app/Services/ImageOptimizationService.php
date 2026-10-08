<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service ImageOptimizationService
 * Mengonversi setiap file gambar (.jpg, .jpeg, .png) menjadi format .webp berkualitas 82%
 * untuk menghemat bandwidth, mempercepat load time (<1.5 detik), dan efisiensi penyimpanan.
 */
class ImageOptimizationService
{
    /**
     * Folder default penyimpanan media
     */
    protected string $disk = 'public';

    /**
     * Alias method untuk optimize dan simpan gambar
     */
    public function optimizeAndStore(UploadedFile|string $file, string $directory = 'products', int $quality = 85, ?int $maxDimension = null): string
    {
        return $this->convertToWebp($file, $directory, $quality, $maxDimension);
    }

    /**
     * Alias method untuk hapus gambar
     */
    public function delete(?string $path): bool
    {
        return $this->deleteImage($path);
    }

    /**
     * Smart Auto-Resize dan konversi gambar ke format .webp
     *
     * @param UploadedFile|string $file File upload atau path lokal
     * @param string $directory Sub-folder di dalam storage (misal: 'products', 'categories', 'banners')
     * @param int $quality Kualitas kompresi WebP (default 85%)
     * @param int|null $maxDimension Dimensi maksimum panjang/lebar (px)
     * @return string Path relatif file yang tersimpan (misal: 'products/abc12345.webp')
     */
    public function convertToWebp(UploadedFile|string $file, string $directory = 'products', int $quality = 85, ?int $maxDimension = null): string
    {
        $filename = Str::uuid() . '.webp';
        $relativeDir = trim($directory, '/');
        $targetPath = $relativeDir . '/' . $filename;

        // Tentukan batas dimensi maksimum otomatis berdasarkan direktori
        if ($maxDimension === null) {
            $maxDimension = str_starts_with($relativeDir, 'banners') ? 2000 : 1600;
        }

        // Pastikan folder tujuan ada
        Storage::disk($this->disk)->makeDirectory($relativeDir);
        $fullDestinationPath = Storage::disk($this->disk)->path($targetPath);

        try {
            $sourcePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

            if (function_exists('imagewebp') && function_exists('imagecreatefromstring')) {
                $imageData = file_get_contents($sourcePath);
                $sourceImage = @imagecreatefromstring($imageData);

                if ($sourceImage !== false) {
                    $origWidth = imagesx($sourceImage);
                    $origHeight = imagesy($sourceImage);

                    // 1. SMART AUTO-RESIZE: Hitung dimensi baru jika melebihi batas
                    $targetWidth = $origWidth;
                    $targetHeight = $origHeight;

                    if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                        if ($origWidth >= $origHeight) {
                            $targetWidth = $maxDimension;
                            $targetHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
                        } else {
                            $targetHeight = $maxDimension;
                            $targetWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
                        }
                    }

                    // 2. Buat canvas gambar baru jika perlu resize
                    if ($targetWidth !== $origWidth || $targetHeight !== $origHeight) {
                        $processedImage = imagecreatetruecolor($targetWidth, $targetHeight);

                        // Pertahankan transparansi PNG / Alpha channel
                        imagealphablending($processedImage, false);
                        imagesavealpha($processedImage, true);
                        $transparent = imagecolorallocatealpha($processedImage, 255, 255, 255, 127);
                        imagefilledrectangle($processedImage, 0, 0, $targetWidth, $targetHeight, $transparent);

                        imagecopyresampled(
                            $processedImage,
                            $sourceImage,
                            0, 0, 0, 0,
                            $targetWidth,
                            $targetHeight,
                            $origWidth,
                            $origHeight
                        );

                        imagedestroy($sourceImage);
                    } else {
                        $processedImage = $sourceImage;
                        imagepalettetotruecolor($processedImage);
                        imagealphablending($processedImage, true);
                        imagesavealpha($processedImage, true);
                    }

                    // 3. Simpan sebagai WebP berkualitas tinggi & efisien
                    imagewebp($processedImage, $fullDestinationPath, $quality);
                    imagedestroy($processedImage);

                    return $targetPath;
                }
            }

            if (class_exists('\Imagick')) {
                $imagick = new \Imagick($sourcePath);
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality($quality);

                // Auto resize Imagick
                if ($imagick->getImageWidth() > $maxDimension || $imagick->getImageHeight() > $maxDimension) {
                    $imagick->resizeImage($maxDimension, $maxDimension, \Imagick::FILTER_LANCZOS, 1, true);
                }

                $imagick->writeImage($fullDestinationPath);
                $imagick->clear();
                $imagick->destroy();

                return $targetPath;
            }

            // Fallback: simpan langsung file jika extension GD/Imagick belum terpasang
            if ($file instanceof UploadedFile) {
                $file->storeAs($relativeDir, $filename, $this->disk);
            } else {
                copy($sourcePath, $fullDestinationPath);
            }

            return $targetPath;
        } catch (\Throwable $e) {
            Log::error('Gagal mengonversi gambar ke WebP: ' . $e->getMessage(), [
                'file' => $file instanceof UploadedFile ? $file->getClientOriginalName() : $file,
            ]);

            // Fallback penyimpanan darurat
            if ($file instanceof UploadedFile) {
                return $file->store($relativeDir, $this->disk);
            }

            return $targetPath;
        }
    }

    /**
     * Hapus file gambar dari public storage
     *
     * @param string|null $path
     * @return bool
     */
    public function deleteImage(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Ambil URL publik dari gambar
     *
     * @param string|null $path
     * @return string|null
     */
    public function getUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }
}
