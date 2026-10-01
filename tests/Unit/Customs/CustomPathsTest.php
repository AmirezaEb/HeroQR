<?php

namespace HeroQR\Tests\Unit\Customs;

use HeroQR\Customs\{MarkerPaths,ShapePaths,CursorPaths};
use PHPUnit\Framework\{Attributes\Test, TestCase};

/**
 * Class CustomPathsTest
 * Tests CursorPaths, MarkerPaths, and ShapePaths classes.
 */
class CustomPathsTest extends TestCase
{
    /**
     * Test CursorPaths: Check if constants exist and files are readable
     */
    #[Test]
    public function isAllCursorPathsExist(): void
    {
        $paths = CursorPaths::getAllPaths();

        $this->assertCount(7, $paths, "CursorPaths should have 7 constants starting with 'C'.");

        foreach ($paths as $path) {
            $this->assertFileExists($path, "Cursor asset file not found at: $path");
            $this->assertStringEndsWith('.png', $path);
        }
    }

    /**
     * Test MarkerPaths: Check if constants exist and files are readable
     */
    #[Test]
    public function isAllMarkerPathsExist(): void
    {
        $paths = MarkerPaths::getAllPaths();

        $this->assertCount(7, $paths, "MarkerPaths should have 7 constants starting with 'M'.");

        foreach ($paths as $path) {
            $this->assertFileExists($path, "Marker asset file not found at: $path");
            $this->assertStringEndsWith('.png', $path);
        }
    }

    /**
     * Test ShapePaths: Check if constants match the expected drawing methods
     */
    #[Test]
    public function isShapePathsAreCorrectStrings(): void
    {
        $paths = ShapePaths::getAllPaths();

        $this->assertCount(4, $paths);
        $this->assertContains('drawSquare', $paths);
        $this->assertContains('drawCircle', $paths);
        $this->assertContains('drawStar', $paths);
        $this->assertContains('drawDiamond', $paths);
    }
}