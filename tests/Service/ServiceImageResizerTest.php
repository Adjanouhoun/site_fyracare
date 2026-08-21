<?php

namespace App\Tests\Service;

use App\Service\ServiceImageResizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

final class ServiceImageResizerTest extends TestCase
{
    public function testLargeImageIsResizedWithoutChangingItsProportions(): void
    {
        $projectDir = sys_get_temp_dir().'/fyracare-image-'.bin2hex(random_bytes(4));
        $uploadDir = $projectDir.'/public/uploads/services';
        mkdir($uploadDir, 0777, true);
        $source = imagecreatetruecolor(2000, 1000);
        imagejpeg($source, $uploadDir.'/large.jpg', 90);
        imagedestroy($source);
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getProjectDir')->willReturn($projectDir);

        (new ServiceImageResizer($kernel))->resize('large.jpg');

        [$width, $height] = getimagesize($uploadDir.'/large.jpg');
        self::assertSame(1600, $width);
        self::assertSame(800, $height);
        unlink($uploadDir.'/large.jpg');
        rmdir($uploadDir);
        rmdir(dirname($uploadDir));
        rmdir(dirname(dirname($uploadDir)));
        rmdir($projectDir);
    }
}
