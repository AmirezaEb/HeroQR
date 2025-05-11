<?php

namespace HeroQR\Tests\Unit\Customs;

use HeroQR\Customs\ShapeDrawers;
use PHPUnit\Framework\{Attributes\Test, TestCase};

/**
 * Class ShapeDrawersTest
 * Tests the ShapeDrawers class.
 */
class ShapeDrawersTest extends TestCase
{
    private false|\GdImage $image;
    private int|false $foregroundColor;

    /**
     * Setup method
     */
    protected function setUp(): void
    {
        $this->image = imagecreatetruecolor(100, 100);
        $this->foregroundColor = imagecolorallocate($this->image, 255, 0, 0);
        $backgroundColor = imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $backgroundColor);
    }

    /**
     * TearDown method
     */
    protected function tearDown(): void
    {
        imagedestroy($this->image);
    }

    /**
     * Test drawing a square
     */
    #[Test]
    public function isCanDrawSquare(): void
    {
        ShapeDrawers::drawSquare($this->image, 1, 1, 20, $this->foregroundColor);

        $coloredPixelCount = $this->countColoredPixels($this->foregroundColor);
        $this->assertGreaterThan(50, $coloredPixelCount, "Square shape was not drawn correctly.");
    }

    /**
     * Test drawing a circle
     */
    #[Test]
    public function isCanDrawCircle(): void
    {
        ShapeDrawers::drawCircle($this->image, 1, 1, 20, $this->foregroundColor);

        $coloredPixelCount = $this->countColoredPixels($this->foregroundColor);
        $this->assertGreaterThan(50, $coloredPixelCount, "Circle shape was not drawn correctly.");
    }

    /**
     * Count the number of pixels in the image that match the expected color
     */
    private function countColoredPixels(int $expectedColor): int
    {
        $width = imagesx($this->image);
        $height = imagesy($this->image);
        $count = 0;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                if (imagecolorat($this->image, $x, $y) === $expectedColor) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Test drawing a star.
     */
    #[Test]
    public function isCanDrawStar(): void
    {
        ShapeDrawers::drawStar($this->image, 1, 1, 20, $this->foregroundColor);

        $coloredPixelCount = $this->countColoredPixels($this->foregroundColor);
        $this->assertGreaterThan(50, $coloredPixelCount, "Star shape was not drawn correctly.");
    }

    /**
     * Test drawing a diamond.
     */
    #[Test]
    public function isCanDrawDiamond(): void
    {
        ShapeDrawers::drawDiamond($this->image, 1, 1, 20, $this->foregroundColor);

        $coloredPixelCount = $this->countColoredPixels($this->foregroundColor);
        $this->assertGreaterThan(50, $coloredPixelCount, "Diamond shape was not drawn correctly.");
    }
}
