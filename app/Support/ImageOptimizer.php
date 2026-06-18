<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    private const MAX_WIDTH = 1920;

    private const WEBP_QUALITY = 82;

    public static function canOptimize(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatefromjpeg');
    }

    public static function toWebp(?string $path): ?string
    {
        if (! filled($path) || ! static::canOptimize()) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['gif', 'svg', 'webp'], true)) {
            return null;
        }

        $fullPath = $disk->path($path);

        $image = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($fullPath),
            'png' => @imagecreatefrompng($fullPath),
            default => null,
        };

        if (! $image) {
            Log::warning('ImageOptimizer: cannot read image', ['path' => $path]);

            return null;
        }

        if ($ext === 'png') {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        $image = static::resizeIfNeeded($image);

        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path) ?: $path . '.webp';
        $ok = imagewebp($image, $disk->path($webpPath), self::WEBP_QUALITY);
        imagedestroy($image);

        if (! $ok) {
            Log::warning('ImageOptimizer: imagewebp failed', ['path' => $path]);

            return null;
        }

        return $webpPath;
    }

    public static function webpPath(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);

        return Storage::disk('public')->exists($webpPath) ? $webpPath : null;
    }

    public static function deleteVariants(?string $path): void
    {
        if (! filled($path)) {
            return;
        }

        $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);

        Storage::disk('public')->delete($webpPath);
    }

    /**
     * @return \GdImage
     */
    private static function resizeIfNeeded(\GdImage $image): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= self::MAX_WIDTH) {
            return $image;
        }

        $ratio = self::MAX_WIDTH / $width;
        $newWidth = self::MAX_WIDTH;
        $newHeight = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }
}