<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;

final class ServiceImageResizer
{
    private string $projectDir;
    public function __construct(KernelInterface $kernel) { $this->projectDir = $kernel->getProjectDir(); }

    public function resize(?string $filename, int $maximum = 1600): void
    {
        if (!$filename) return;
        $path = $this->projectDir.'/public/uploads/services/'.$filename;
        if (!is_file($path)) return;
        $info = @getimagesize($path);
        if (!$info || max($info[0], $info[1]) <= $maximum) return;

        [$width, $height, $type] = $info;
        $ratio = min($maximum / $width, $maximum / $height);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));
        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default => false,
        };
        if (!$source) return;
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
}
