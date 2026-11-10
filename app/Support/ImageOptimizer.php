<?php

namespace App\Support;

use Throwable;

class ImageOptimizer
{
    /**
     * Perkecil dimensi dan kompres foto agar halaman pengunjung tidak lambat.
     */
    public static function optimize(string $path, int $maxDimension = 1600, int $quality = 88): void
    {
        try {
            if ($path === '' || ! is_file($path)) {
                return;
            }

            $info = @getimagesize($path);
            if (! $info || empty($info['mime'])) {
                return;
            }

            if (filesize($path) <= 300 * 1024 && max($info[0], $info[1]) <= $maxDimension) {
                return;
            }

            $image = @imagecreatefromstring((string) file_get_contents($path));
            if (! $image) {
                return;
            }

            $image = self::applyExifOrientation($image, $path, (string) $info['mime']);

            $width = imagesx($image);
            $height = imagesy($image);
            $scale = min(1, $maxDimension / max($width, $height));

            if ($scale < 1) {
                $newWidth = max(1, (int) round($width * $scale));
                $newHeight = max(1, (int) round($height * $scale));
                $canvas = imagecreatetruecolor($newWidth, $newHeight);

                if (in_array($info['mime'], ['image/png', 'image/webp'], true)) {
                    imagealphablending($canvas, false);
                    imagesavealpha($canvas, true);
                    imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
                }

                imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $canvas;
            }

            match ($info['mime']) {
                'image/jpeg' => imagejpeg($image, $path, $quality),
                'image/png' => imagepng($image, $path, 8),
                'image/webp' => imagewebp($image, $path, $quality),
                default => null,
            };

            imagedestroy($image);
        } catch (Throwable) {
            // Foto tetap dipakai apa adanya bila kompresi gagal.
        }
    }

    private static function applyExifOrientation($image, string $path, string $mime)
    {
        if ($mime !== 'image/jpeg' || ! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated) {
            imagedestroy($image);
            $image = $rotated;
        }

        if ($orientation === 2) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        } elseif ($orientation === 4) {
            imageflip($image, IMG_FLIP_VERTICAL);
        }

        return $image;
    }
}
