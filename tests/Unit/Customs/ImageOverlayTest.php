<?php

namespace HeroQR\Tests\Unit\Customs;

use PHPUnit\Framework\TestCase;
use HeroQR\Customs\ImageOverlay;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class ImageOverlayTest
 * Tests the ImageOverlay class.
 */
class ImageOverlayTest extends TestCase
{
    private string $validBackgroundKey;
    private string $validOverlayKey;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $this->validBackgroundKey = 'M' . mt_rand(1, 3);
        $this->validOverlayKey = 'C' . mt_rand(1, 3);
    }

    /**
     * Test the constructor with valid keys
     */
    #[Test]
    public function isConstructorWithValidKeys(): void
    {
        $imageOverlay = new ImageOverlay($this->validBackgroundKey, $this->validOverlayKey, []);
        $this->assertInstanceOf(ImageOverlay::class, $imageOverlay);
    }

    /**
     * Test the constructor with invalid keys
     */
    #[Test]
    public function isConstructorWithInvalidKeys(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Invalid key \'.+\' provided. Valid keys are : .+/');

        new ImageOverlay('InvalidKey', 'AnotherInvalidKey', []);
    }

    /**
     * Test saving the image with valid paths
     */
    #[Test]
    public function isSaveImage(): void
    {
        $imageOverlay = new ImageOverlay($this->validBackgroundKey, $this->validOverlayKey, []);
        $outputPath = __DIR__ . '/output.png';
        $imageOverlay->saveImage($outputPath);

        $this->assertFileExists($outputPath);

        @unlink($outputPath);
    }
}
