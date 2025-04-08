<?php

declare(strict_types=1);

namespace HeroQR\Contracts\Customs;

use Endroid\QrCode\{Matrix\MatrixInterface, QrCodeInterface};
use Endroid\QrCode\{Color\ColorInterface, Logo\LogoInterface};
use HeroQR\Customs\{ImageOverlay, ShapeDrawers};

/**
 * Handles the rendering of QR codes using GD image processing library.
 *
 * This class provides methods for drawing QR code matrices, handling image overlays,
 * and preparing corner images for QR codes. It serves as a base for specific QR code
 * renderers that utilize GD to create QR code images.
 *
 * @package HeroQR\Contracts\Customs
 */
final class Drawer
{
    private const FINDER_PATTERN_SIZE = 7;

    /**
     * Draws the QR code on the base image, including corner images and matrix.
     *
     * Prepares and resizes the corner image, then renders the QR code matrix
     * with the specified block size, color, and shape, and applies the image overlay.
     *
     * @param \GdImage $baseImage The base image for drawing the QR code.
     * @param MatrixInterface $matrix The QR code matrix.
     * @param int $baseBlockSize The block size for the QR code.
     * @param int $foregroundColor The color of the QR code blocks.
     * @param QrCodeInterface $qrCode The QR code object.
     * @param string $blockShape The shape of the QR code blocks.
     * @param ImageOverlay $imageOverlay Optional image overlay.
     * @return void
     * @throws \Exception
     */
    public static function drawQrCode(
        \GdImage        $baseImage,
        MatrixInterface $matrix,
        int             $baseBlockSize,
        int             $foregroundColor,
        QrCodeInterface $qrCode,
        string          $blockShape,
        ImageOverlay    $imageOverlay
    ): void
    {
        $cornerImage = self::prepareCornerImage($qrCode, $imageOverlay);
        $resizedCorner = self::resizeCornerImage($cornerImage, $baseBlockSize);
        self::drawMatrix($baseImage, $matrix, $baseBlockSize, $foregroundColor, $resizedCorner, $blockShape, null, $qrCode->getData());

        imagedestroy($cornerImage);
        imagedestroy($resizedCorner);
    }

    /**
     * Draws the QR code matrix onto the base image.
     *
     * @param \GdImage $baseImage The base image for the QR code.
     * @param MatrixInterface $matrix The QR code matrix.
     * @param int $baseBlockSize The size of each block in the QR code.
     * @param int $foregroundColor The fill color for the blocks.
     * @param \GdImage $resizedCorner Resized corner image for rounded blocks.
     * @param string $blockShape The shape of the block in the QR code
     * @param LogoInterface|null $logo The logo to embed in the QR code (optional).
     * @param string|null $data The data encoded in the QR code (optional).
     * @return void
     */
    public static function drawMatrix(
        \GdImage        $baseImage,
        MatrixInterface $matrix,
        int             $baseBlockSize,
        int             $foregroundColor,
        \GdImage        $resizedCorner,
        string          $blockShape,
        ?LogoInterface  $logo = null,
        ?string         $data = null
    ): void
    {
        $blockCount = $matrix->getBlockCount();
        [$logoWidth, $logoHeight, $padding] = self::prepareLogoDimensions($logo, $data, $baseBlockSize);

        [$logoStartRow, $logoEndRow, $logoStartCol, $logoEndCol] = self::calculateLogoBounds([
            'blockCount' => $blockCount,
            'width' => $logoWidth,
            'height' => $logoHeight,
            'padding' => $padding
        ]);

        for ($rowIndex = 0; $rowIndex < $blockCount; ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $blockCount; ++$columnIndex) {
                if (self::isWithinLogoBounds([
                    'rowIndex' => $rowIndex,
                    'columnIndex' => $columnIndex,
                    'logoStartRow' => intval($logoStartRow),
                    'logoEndRow' => intval($logoEndRow),
                    'logoStartCol' => intval($logoStartCol),
                    'logoEndCol' => intval($logoEndCol)
                ])) {
                    continue;
                }

                if ($matrix->getBlockValue($rowIndex, $columnIndex) === 1) {
                    self::drawMatrixBlock($baseImage, $matrix, $rowIndex, $columnIndex, $baseBlockSize, $foregroundColor, $blockShape, $resizedCorner);
                }
            }
        }
    }

    /**
     * Draws a block of the matrix on the QR code image, handling special corner blocks and filling others with a foreground color
     *
     * @param \GdImage $baseImage The base image where the block will be drawn
     * @param MatrixInterface $matrix The matrix representing the QR code
     * @param int $rowIndex The row index of the block
     * @param int $columnIndex The column index of the block
     * @param int $baseBlockSize The size of each block in the QR code
     * @param int $foregroundColor The color to fill the block
     * @param string $blockShape The shape of the block in the QR code
     * @param \GdImage $resizedCorner The resized corner image (used for corner blocks)
     * @return void
     */
    private static function drawMatrixBlock(
        \GdImage        $baseImage,
        MatrixInterface $matrix,
        int             $rowIndex,
        int             $columnIndex,
        int             $baseBlockSize,
        int             $foregroundColor,
        string          $blockShape,
        \GdImage        $resizedCorner
    ): void
    {
        $isTopLeft = $rowIndex < self::FINDER_PATTERN_SIZE && $columnIndex < self::FINDER_PATTERN_SIZE;
        $isTopRight = $rowIndex < self::FINDER_PATTERN_SIZE && $columnIndex >= $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE;
        $isBottomLeft = $rowIndex >= $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE && $columnIndex < self::FINDER_PATTERN_SIZE;

        if ($isTopLeft || $isTopRight || $isBottomLeft) {
            self::drawCornerBlock($baseImage, $matrix, $rowIndex, $columnIndex, $baseBlockSize, $resizedCorner, $isTopLeft, $isTopRight, $isBottomLeft);
        } else {
            ShapeDrawers::{$blockShape}($baseImage, $rowIndex, $columnIndex, $baseBlockSize, $foregroundColor);
        }
    }

    /**
     * Draws a corner block on the QR code image (top-left, top-right, or bottom-left)
     *
     * @param \GdImage $baseImage The base image where the corner block will be drawn
     * @param MatrixInterface $matrix The matrix representing the QR code
     * @param int $rowIndex The row index of the block
     * @param int $columnIndex The column index of the block
     * @param int $baseBlockSize The size of each block in the QR code
     * @param \GdImage $resizedCorner The resized corner image
     * @param bool $isTopLeft Whether the corner is top-left
     * @param bool $isTopRight Whether the corner is top-right
     * @param bool $isBottomLeft Whether the corner is bottom-left
     * @return void
     */
    private static function drawCornerBlock(
        \GdImage        $baseImage,
        MatrixInterface $matrix,
        int             $rowIndex,
        int             $columnIndex,
        int             $baseBlockSize,
        \GdImage        $resizedCorner,
        bool            $isTopLeft,
        bool            $isTopRight,
        bool            $isBottomLeft
    ): void
    {
        if (self::rotateCornerImage($rowIndex, $matrix, $isTopLeft, $isTopRight, $isBottomLeft, $columnIndex)) {
            $rotatedCorner = $resizedCorner;

            if ($isTopRight) {
                $rotatedCorner = imagerotate($resizedCorner, 270, imagecolorallocatealpha($resizedCorner, 0, 0, 0, 127));
            } elseif ($isBottomLeft) {
                $rotatedCorner = imagerotate($resizedCorner, 90, imagecolorallocatealpha($resizedCorner, 0, 0, 0, 127));
            }

            self::copyResampledImage(
                $baseImage,
                $rotatedCorner,
                ['X' => intval($columnIndex * $baseBlockSize), 'Y' => intval($rowIndex * $baseBlockSize), 'Width' => self::FINDER_PATTERN_SIZE * $baseBlockSize, 'Height' => self::FINDER_PATTERN_SIZE * $baseBlockSize],
                ['X' => 0, 'Y' => 0, 'Width' => self::FINDER_PATTERN_SIZE * $baseBlockSize, 'Height' => self::FINDER_PATTERN_SIZE * $baseBlockSize,]
            );
        }
    }

    /**
     * Checks if the corner image needs to be rotated based on the matrix position.
     *
     * @param int $rowIndex The current row index in the matrix.
     * @param MatrixInterface $matrix The matrix object.
     * @param bool $isTopLeft Whether it's the top-left corner.
     * @param bool $isTopRight Whether it's the top-right corner.
     * @param bool $isBottomLeft Whether it's the bottom-left corner.
     * @param int $columnIndex The current column index in the matrix.
     * @return bool True if the corner image needs to be rotated, false otherwise.
     */
    private static function rotateCornerImage(int $rowIndex, MatrixInterface $matrix, bool $isTopLeft, bool $isTopRight, bool $isBottomLeft, int $columnIndex): bool
    {
        return ($isTopLeft && $rowIndex === 0 && $columnIndex === 0) ||
            ($isTopRight && $rowIndex === 0 && $columnIndex === $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE) ||
            ($isBottomLeft && $rowIndex === $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE && $columnIndex === 0);
    }

    /**
     * Resamples and copies a portion of the source image to the destination image
     *
     * @param \GdImage $dstImage Destination image resource
     * @param \GdImage $srcImage Source image resource
     * @param array $dst_X_Y_W_H Destination coordinates and dimensions (X, Y, Width, Height)
     * @param array $src_X_Y_W_H Source coordinates and dimensions (X, Y, Width, Height)
     * @return bool True on success, false on failure
     */
    public static function copyResampledImage(
        \GdImage $dstImage,
        \GdImage $srcImage,
        array    $dst_X_Y_W_H,
        array    $src_X_Y_W_H
    ): bool
    {
        return imagecopyresampled(
            $dstImage,
            $srcImage,
            $dst_X_Y_W_H['X'],
            $dst_X_Y_W_H['Y'],
            $src_X_Y_W_H['X'],
            $src_X_Y_W_H['Y'],
            $dst_X_Y_W_H['Width'],
            $dst_X_Y_W_H['Height'],
            $src_X_Y_W_H['Width'],
            $src_X_Y_W_H['Height'],
        );
    }

    /**
     * Prepares the logo dimensions and calculates the necessary padding based on the data length.
     *
     * @param LogoInterface|null $logo The logo to be used in the QR code.
     * @param string|null $data The data for the QR code.
     * @param int $baseBlockSize The base size of the QR code blocks.
     * @return array The width, height, and padding for the logo.
     */
    private static function prepareLogoDimensions(?LogoInterface $logo, ?string $data, int $baseBlockSize): array
    {
        if ($logo === null) {
            return [0, 0, 0];
        }

        $length = strlen($data ?? '');
        [$logoWidth, $logoHeight] = self::calculateLogoDimensions($logo, $baseBlockSize);

        $padding = match (true) {
            $length >= 380 => 7,
            $length >= 260 => 6,
            $length >= 150 => 5,
            $length >= 80 => 4,
            $length >= 25 => 3,
            $length >= 10 => 2,
            default => 1,
        };

        return [$logoWidth, $logoHeight, $padding];
    }

    /**
     * Calculates the bounds for the logo's placement within the QR code.
     *
     * @param array $logo Contains logo dimensions, block count, and padding information.
     * @return array The start and end row and column indices for logo placement.
     */
    private static function calculateLogoBounds(array $logo): array
    {
        $logoStartRow = max(0, ($logo['blockCount'] - $logo['height']) / 2 - $logo['padding']);
        $logoEndRow = min($logo['blockCount'], ($logo['blockCount'] + $logo['height']) / 2 + $logo['padding']);
        $logoStartCol = max(0, ($logo['blockCount'] - $logo['width']) / 2 - $logo['padding']);
        $logoEndCol = min($logo['blockCount'], ($logo['blockCount'] + $logo['width']) / 2 + $logo['padding']);

        return [$logoStartRow, $logoEndRow, $logoStartCol, $logoEndCol];
    }

    /**
     * Checks if the given row and column are within the calculated logo bounds.
     *
     * @param array $columns Contains row and column indices, and logo bounds.
     * @return bool True if the position is within the logo bounds, false otherwise.
     */
    private static function isWithinLogoBounds(array $columns): bool
    {
        return $columns['rowIndex'] >= $columns['logoStartRow'] && $columns['rowIndex'] < $columns['logoEndRow'] &&
            $columns['rowIndex'] >= $columns['logoStartCol'] && $columns['rowIndex'] < $columns['logoEndCol'];
    }

    /**
     * Calculates the dimensions of the logo based on the base block size.
     *
     * @param LogoInterface|null $logo The logo to be resized.
     * @param int $baseBlockSize The base size of QR code blocks.
     * @return array The resized width and height of the logo in blocks.
     */
    private static function calculateLogoDimensions(?LogoInterface $logo, int $baseBlockSize): array
    {
        return $logo === null
            ? [0, 0] : [
                (int)ceil($logo->getResizeToWidth() / $baseBlockSize),
                (int)ceil($logo->getResizeToHeight() / $baseBlockSize)
            ];
    }

    /**
     * Prepares the corner image for the QR code
     *
     * @param QrCodeInterface $qrCode The QR code object
     * @return \GdImage The prepared corner image
     * @throws \Exception If the corner image cannot be loaded
     */
    private static function prepareCornerImage(QrCodeInterface $qrCode, ImageOverlay $imageOverlay): \GdImage
    {
        $cornerImage = $imageOverlay->getImage();

        imageantialias($cornerImage, true);
        imagesavealpha($cornerImage, true);

        return self::tintImage($cornerImage, $qrCode->getForegroundColor());
    }

    /**
     * Tints the image with a specified color, applying the color to non-transparent pixels
     *
     * @param \GdImage $image The image to tint
     * @param ColorInterface $color The color to apply to the image
     * @return \GdImage The tinted image
     */
    private static function tintImage(\GdImage $image, ColorInterface $color): \GdImage
    {
        [$width, $height] = [imagesx($image), imagesy($image)];

        $tinted = imagecreatetruecolor($width, $height);

        imagesavealpha($tinted, true);
        imagealphablending($tinted, false);

        $transparent = imagecolorallocatealpha($tinted, 0, 0, 0, 127);
        imagefill($tinted, 0, 0, $transparent);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $pixelColor = imagecolorsforindex($image, imagecolorat($image, $x, $y));

                if ($pixelColor['alpha'] < 120) {
                    $newColor = imagecolorallocatealpha(
                        $tinted,
                        $color->getRed(),
                        $color->getGreen(),
                        $color->getBlue(),
                        $color->getAlpha()
                    );
                    imagesetpixel($tinted, $x, $y, $newColor);
                }
            }
        }
        return $tinted;
    }

    /**
     * Resizes the corner image to fit the QR code matrix
     *
     * @param \GdImage $cornerImage The original corner image
     * @param int $baseBlockSize The size of the base blocks in the QR code
     * @return \GdImage The resized corner image
     */
    private static function resizeCornerImage(\GdImage $cornerImage, int $baseBlockSize): \GdImage
    {
        $cornerSize = self::FINDER_PATTERN_SIZE * $baseBlockSize;
        $resizedCorner = imagecreatetruecolor($cornerSize, $cornerSize);

        imageantialias($resizedCorner, true);
        imagesavealpha($resizedCorner, true);
        imagealphablending($resizedCorner, false);

        $transparent = imagecolorallocatealpha($resizedCorner, 0, 0, 0, 127);
        imagefill($resizedCorner, 0, 0, $transparent);

        imagealphablending($resizedCorner, true);

        Drawer::copyResampledImage(
            $resizedCorner,
            $cornerImage,
            ['X' => 0, 'Y' => 0, 'Width' => $cornerSize, 'Height' => $cornerSize,],
            ['X' => 0, 'Y' => 0, 'Width' => imagesy($cornerImage), 'Height' => imagesy($cornerImage)]
        );

        return $resizedCorner;
    }
}
