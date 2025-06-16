<?php

namespace HeroQR\Customs;

/**
 * Class ShapeDrawers
 *
 * This class provides static methods to draw various shapes (star, square, circle, diamond)
 * on a GD image resource. Each method is designed to handle specific shape-drawing logic
 * based on the provided parameters such as position, size, and color.
 *
 * @package HeroQR\Customs
 */
class ShapeDrawers
{
    /**
     * Draws a star shape on the image
     */
    public static function drawStar(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        $points = [];
        for ($i = 0; $i < 10; $i++) {
            $angle = $i * M_PI / 5 + M_PI / 2;
            $radius = ($i % 2 === 0) ? $baseBlockSize / 2 : $baseBlockSize / 4;
            $points[] = intval($columnIndex * $baseBlockSize + $baseBlockSize / 2 + $radius * cos($angle));
            $points[] = intval($rowIndex * $baseBlockSize + $baseBlockSize / 2 - $radius * sin($angle));
        }
        imagefilledpolygon($baseImage, $points, $foregroundColor);
    }


    /**
     * Draws a square shape on the image
     */
    public static function drawSquare(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void {
        imagefilledrectangle(
            $baseImage,
            $columnIndex * $baseBlockSize,
            $rowIndex * $baseBlockSize,
            ($columnIndex + 1) * $baseBlockSize - 1,
            ($rowIndex + 1) * $baseBlockSize - 1,
            $foregroundColor
        );
    }

    /**
     * Draws a circle shape on the image
     */
    public static function drawCircle(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
        imagefilledellipse(
            $baseImage,
            intval($columnIndex * $baseBlockSize + $baseBlockSize / 2),
            intval($rowIndex * $baseBlockSize + $baseBlockSize / 2),
            intval($baseBlockSize * 0.8),
            intval($baseBlockSize * 0.8),
            $foregroundColor
        );
    }

    /**
     * Draws a diamond shape on the image
     */
    public static function drawDiamond(
        \GdImage $baseImage,
        int      $rowIndex,
        int      $columnIndex,
        int      $baseBlockSize,
        int      $foregroundColor
    ): void
    {
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