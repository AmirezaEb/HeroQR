<?php

namespace HeroQR\Customs\ShapeDrawers;

/**
 * Provides static methods for drawing basic shapes on GD images,
 * including stars, squares, circles, and diamonds.
 */
class PngShapeDrawer
{
    /**
     * Draws a star shape on the image
     *
     * @param \GdImage $baseImage
     * @param int $rowIndex
     * @param int $columnIndex
     * @param int $baseBlockSize
     * @param int $foregroundColor
     * @return void
     */
    public static function drawStar(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;

        $points = [];

        for ($i = 0; $i < 10; ++$i) {
            $angle = $i * M_PI / 5 + M_PI / 2;
            $radius = ($i % 2 === 0) ? $baseBlockSize / 2 : $baseBlockSize / 4;
            $points[] = (int)($centerX + $radius * cos($angle));
            $points[] = (int)($centerY - $radius * sin($angle));
        }

        imagefilledpolygon($baseImage, $points, $foregroundColor);
    }

    /**
     * Draws a square shape on the image
     *
     * @param \GdImage $baseImage
     * @param int $rowIndex
     * @param int $columnIndex
     * @param int $baseBlockSize
     * @param int $foregroundColor
     * @return void
     */
    public static function drawSquare(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $x = $columnIndex * $baseBlockSize;
        $y = $rowIndex * $baseBlockSize;

        imagefilledrectangle(
            $baseImage,
            $x,
            $y,
            $x + $baseBlockSize - 1,
            $y + $baseBlockSize - 1,
            $foregroundColor
        );
    }

    /**
     * Draws a circle shape on the image
     *
     * @param \GdImage $baseImage
     * @param int $rowIndex
     * @param int $columnIndex
     * @param int $baseBlockSize
     * @param int $foregroundColor
     * @return void
     */
    public static function drawCircle(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $centerX = (int)($columnIndex * $baseBlockSize + $baseBlockSize / 2);
        $centerY = (int)($rowIndex * $baseBlockSize + $baseBlockSize / 2);
        $size = (int)($baseBlockSize * 0.8);

        imagefilledellipse(
            $baseImage,
            $centerX,
            $centerY,
            $size,
            $size,
            $foregroundColor
        );
    }

    /**
     * Draws a diamond shape on the image
     *
     * @param \GdImage $baseImage
     * @param int $rowIndex
     * @param int $columnIndex
     * @param int $baseBlockSize
     * @param int $foregroundColor
     * @return void
     */
    public static function drawDiamond(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $centerX = $columnIndex * $baseBlockSize + $baseBlockSize / 2;
        $centerY = $rowIndex * $baseBlockSize + $baseBlockSize / 2;
        $halfSize = $baseBlockSize / 2.2;

        imagefilledpolygon($baseImage, [
            $centerX,
            $centerY - $halfSize,
            $centerX + $halfSize,
            $centerY,
            $centerX,
            $centerY + $halfSize,
            $centerX - $halfSize,
            $centerY,
        ], $foregroundColor);
    }
}