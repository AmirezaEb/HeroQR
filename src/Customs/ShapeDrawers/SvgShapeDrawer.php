<?php

namespace HeroQR\Customs\ShapeDrawers;

use Endroid\QrCode\Color\ColorInterface;

/**
 * Provides static methods for drawing basic QR code shapes
 * as reusable SVG elements.
 */
class SvgShapeDrawer
{
    /**
     * Draws a star shape
     *
     * @param \SimpleXMLElement $baseImage
     * @param int $rowIndex
     * @param int $colIndex
     * @param int $baseBlockSize
     * @param \Endroid\QrCode\Color\ColorInterface $foregroundColor
     * @return void
     */
    public static function drawStar(
        \SimpleXMLElement $baseImage,
        int               $rowIndex,
        int               $colIndex,
        int               $baseBlockSize,
        ColorInterface    $foregroundColor
    ): void {
        self::render($baseImage, __FUNCTION__, function ($defs, $blockSize) {
            $points = [];

            $outerRadius = $blockSize / 2;
            $innerRadius = $blockSize / 4;

            for ($i = 0; $i < 10; ++$i) {
                $angle = ($i * M_PI / 5) + (M_PI / 2);
                $radius = ($i % 2 === 0) ? $outerRadius : $innerRadius;
                $points[] = round($radius * cos($angle), 2) . ',' . round(-$radius * sin($angle), 2);
            }

            $star = $defs->addChild('polygon');
            $star->addAttribute('points', implode(' ', $points));

            return $star;

        }, $rowIndex, $colIndex, $baseBlockSize, $foregroundColor);
    }

    /**
     * Draws a square shape
     *
     * @param \SimpleXMLElement $baseImage
     * @param int $rowIndex
     * @param int $colIndex
     * @param int $baseBlockSize
     * @param \Endroid\QrCode\Color\ColorInterface $foregroundColor
     * @return void
     */
    public static function drawSquare(
        \SimpleXMLElement $baseImage,
        int               $rowIndex,
        int               $colIndex,
        int               $baseBlockSize,
        ColorInterface    $foregroundColor
    ): void {

        self::render($baseImage, __FUNCTION__, function ($defs, $blockSize) {
            $halfSize = $blockSize / 2;
            $square = $defs->addChild('rect');
            $square->addAttribute('x', (string)-$halfSize);
            $square->addAttribute('y', (string)-$halfSize);
            $square->addAttribute('width', (string)$blockSize);
            $square->addAttribute('height', (string)$blockSize);

            return $square;

        }, $rowIndex, $colIndex, $baseBlockSize, $foregroundColor);
    }

    /**
     * Draws a circle shape
     *
     * @param \SimpleXMLElement $baseImage
     * @param int $rowIndex
     * @param int $colIndex
     * @param int $baseBlockSize
     * @param \Endroid\QrCode\Color\ColorInterface $foregroundColor
     * @return void
     */
    public static function drawCircle(
        \SimpleXMLElement $baseImage,
        int               $rowIndex,
        int               $colIndex,
        int               $baseBlockSize,
        ColorInterface    $foregroundColor
    ): void {
        self::render($baseImage, __FUNCTION__, function ($defs, $blockSize) {
            $circle = $defs->addChild('circle');
            $circle->addAttribute('cx', '0');
            $circle->addAttribute('cy', '0');
            $circle->addAttribute('r', (string)(($blockSize * 0.8) / 2));

            return $circle;

        }, $rowIndex, $colIndex, $baseBlockSize, $foregroundColor);
    }

    /**
     * Draws a diamond shape
     *
     * @param \SimpleXMLElement $baseImage
     * @param int $rowIndex
     * @param int $colIndex
     * @param int $baseBlockSize
     * @param \Endroid\QrCode\Color\ColorInterface $foregroundColor
     * @return void
     */
    public static function drawDiamond(
        \SimpleXMLElement $baseImage,
        int               $rowIndex,
        int               $colIndex,
        int               $baseBlockSize,
        ColorInterface    $foregroundColor
    ): void {
        self::render($baseImage, __FUNCTION__, function ($defs, $blockSize) {
            $halfSize = $blockSize / 2.2;

            $points = implode(' ', [
                "0,-{$halfSize}",
                "{$halfSize},0",
                "0,{$halfSize}",
                "-{$halfSize},0",
            ]);

            $diamond = $defs->addChild('polygon');
            $diamond->addAttribute('points', $points);

            return $diamond;

        }, $rowIndex, $colIndex, $baseBlockSize, $foregroundColor);
    }

    /**
     * Renders an SVG shape definition or places a <use> reference
     * at the target position.
     */
    private static function render(
        \SimpleXMLElement $baseImage,
        string            $methodName,
        callable          $definitionCallback,
        int               $rowIndex,
        int               $columnIndex,
        int               $blockSize,
        ColorInterface    $foregroundColor
    ): void
    {
        $shapeId = 'shape_' . $methodName;

        if ($baseImage->getName() === 'defs') {
            $element = $definitionCallback($baseImage, $blockSize);

            $element->addAttribute('id', $shapeId);
            $element->addAttribute('fill', $foregroundColor->getHex());

            $alpha = 1 - ($foregroundColor->getAlpha() / 127);

            $element->addAttribute(
                'fill-opacity',
                (string)min(1, max(0, $alpha))
            );

            return;
        }

        $use = $baseImage->addChild('use');
        $use->addAttribute('href', '#' . $shapeId);

        $x = round(($columnIndex * $blockSize) + ($blockSize / 2), 1);
        $y = round(($rowIndex * $blockSize) + ($blockSize / 2), 1);

        $use->addAttribute('x', (string)$x);
        $use->addAttribute('y', (string)$y);
    }
}