<?php

namespace HeroQR\Tests\Unit\Customs;

use Endroid\QrCode\Color\Color;
use HeroQR\Customs\ImageOverlay;
use PHPUnit\Framework\{Attributes\Test, TestCase};

/**
 * Class ImageOverlayOutputTest
 * Extended coverage for ImageOverlay output helpers.
 */
class ImageOverlayOutputTest extends TestCase
{
    private ImageOverlay $imageOverlay;

    /**
     * Setup ImageOverlay with valid marker and cursor keys
     */
    protected function setUp(): void
    {
        $this->imageOverlay = new ImageOverlay('M1', 'C1');
    }

    /**
     * Test getImage returns a GdImage instance
     */
    #[Test]
    public function isGetImageReturnsGdImage(): void
    {
        $image = $this->imageOverlay->getImage();

        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertGreaterThan(0, imagesx($image));
        $this->assertGreaterThan(0, imagesy($image));

        imagedestroy($image);
    }

    /**
     * Test getUriImage returns a PNG data URI
     */
    #[Test]
    public function isGetUriImageReturnsDataUri(): void
    {
        $uri = $this->imageOverlay->getUriImage();

        $this->assertStringStartsWith('data:image/png;base64,', $uri);
        $this->assertNotEmpty(substr($uri, strlen('data:image/png;base64,')));
    }

    /**
     * Test getImageAsString returns raw PNG binary data
     */
    #[Test]
    public function isGetImageAsStringReturnsPngBinary(): void
    {
        $binary = $this->imageOverlay->getImageAsString();

        $this->assertIsString($binary);
        $this->assertStringStartsWith("\x89PNG", $binary);
    }

    /**
     * Test getImage with overlay color still returns a GdImage
     */
    #[Test]
    public function isGetImageWithOverlayColor(): void
    {
        $color = new Color(255, 0, 0);
        $image = $this->imageOverlay->getImage($color);

        $this->assertInstanceOf(\GdImage::class, $image);
        imagedestroy($image);
    }

    /**
     * Test getUriImage with overlay color returns a data URI
     */
    #[Test]
    public function isGetUriImageWithOverlayColor(): void
    {
        $uri = $this->imageOverlay->getUriImage(new Color(0, 128, 255, 0));

        $this->assertStringStartsWith('data:image/png;base64,', $uri);
    }

    /**
     * Test saveImage with overlay color writes a readable PNG file
     */
    #[Test]
    public function isSaveImageWithOverlayColor(): void
    {
        $outputPath = __DIR__ . '/output_colored_' . uniqid() . '.png';

        $this->imageOverlay->saveImage($outputPath, new Color(20, 40, 60));

        $this->assertFileExists($outputPath);
        $this->assertStringStartsWith("\x89PNG", file_get_contents($outputPath));

        @unlink($outputPath);
    }

    /**
     * Test constructor accepts multiple valid marker/cursor pairs
     */
    #[Test]
    public function isConstructorAcceptsMultipleValidPairs(): void
    {
        foreach (['M2', 'M4', 'M6'] as $marker) {
            foreach (['C2', 'C4', 'C6'] as $cursor) {
                $overlay = new ImageOverlay($marker, $cursor);
                $this->assertInstanceOf(ImageOverlay::class, $overlay);
            }
        }
    }

    /**
     * Test constructor rejects invalid marker key
     */
    #[Test]
    public function isConstructorRejectsInvalidMarker(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ImageOverlay('M99', 'C1');
    }

    /**
     * Test constructor rejects invalid cursor key
     */
    #[Test]
    public function isConstructorRejectsInvalidCursor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ImageOverlay('M1', 'C99');
    }
}
