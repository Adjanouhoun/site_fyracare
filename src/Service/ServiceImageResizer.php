<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

final class ServiceImageResizer
{
    private string $projectDir;
    public function __construct(KernelInterface $kernel) { $this->projectDir = $kernel->getProjectDir(); }

    public function resize(?string $filename, int $maximum = 1600): void
    {
        $this->resizeIn('services', $filename, $maximum);
    }

    public function resizeIn(string $directory, ?string $filename, int $maximum = 1600): void
    {
        if (!$filename) return;
        $path = $this->projectDir.'/public/uploads/'.trim($directory, '/').'/'.$filename;
        if (!is_file($path)) return;
        $info = @getimagesize($path);
        if (!$info) return;

        [$width, $height, $type] = $info;
        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default => false,
        };
        if (!$source) return;

        // Les téléphones stockent souvent l'orientation dans les métadonnées EXIF.
        // On l'applique aux pixels avant l'optimisation pour éviter les images couchées.
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
            $source = $this->orientJpeg($source, $orientation);
            if (in_array($orientation, [5, 6, 7, 8], true)) {
                [$width, $height] = [$height, $width];
            }
        }

        if (max($width, $height) <= $maximum && ($orientation ?? 1) === 1) {
            imagedestroy($source);
            return;
        }

        $ratio = min($maximum / $width, $maximum / $height);
        $ratio = min(1, $ratio);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));
        $target = imagecreatetruecolor($newWidth, $newHeight);
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($target, false);
            imagesavealpha($target, true);
        }
        imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        match ($type) {
            IMAGETYPE_JPEG => imagejpeg($target, $path, 88),
            IMAGETYPE_PNG => imagepng($target, $path, 7),
            IMAGETYPE_WEBP => imagewebp($target, $path, 88),
        };
        imagedestroy($source);
        imagedestroy($target);
    }

    private function orientJpeg(\GdImage $image, int $orientation): \GdImage
    {
        if (in_array($orientation, [2, 5, 7], true)) imageflip($image, IMG_FLIP_HORIZONTAL);
        if ($orientation === 4) imageflip($image, IMG_FLIP_VERTICAL);
        return match ($orientation) {
            3 => imagerotate($image, 180, 0),
            5, 6 => imagerotate($image, -90, 0),
            7, 8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }
}
