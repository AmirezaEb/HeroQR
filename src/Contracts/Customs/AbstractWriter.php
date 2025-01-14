<?php

declare(strict_types=1);

namespace HeroQR\Contracts\Customs;

use GdImage;
use HeroQR\Customs\ImageOverlay;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Writer\WriterInterface;
use Endroid\QrCode\Writer\Result\GdResult;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\ImageData\LogoImageData;
use Endroid\QrCode\ImageData\LabelImageData;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\AbstractGdWriter;

/** 
 * Abstract class for GD-based QR code writers, extending AbstractWriter and implementing WriterInterface
 * It provides the base functionality for generating QR code images using the GD library
 */

readonly abstract class AbstractWriter extends AbstractGdWriter implements WriterInterface
{
    private const QUALITY_MULTIPLIER = 10;
    private const FINDER_PATTERN_SIZE = 7;

    private imageOverlay $imageOverlay;

    public function __construct($background, $overlay, $options = [])
    {
        $this->imageOverlay = new ImageOverlay($background, $overlay, $options);
    }

    /**
     * Writes a QR code image with optional logo and label
     *
     * @param QrCodeInterface $qrCode The QR code to be generated
     * @param LogoInterface|null $logo The logo to be embedded in the QR code (optional)
     * @param LabelInterface|null $label The label to be added to the QR code (optional)
     * @param array $options Additional options for the QR code generation
     * 
     * @return ResultInterface The result containing the generated QR code image
     * 
     * @throws \Exception If the GD extension is not loaded
     */
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface $logo = null,
        ?LabelInterface $label = null,
        array $options = []
    ): ResultInterface {
        if (!extension_loaded('gd')) {
            throw new \Exception('Unable to generate image: please check if the GD extension is enabled and configured correctly');
        }

        $matrix = $this->getMatrix($qrCode);
        $baseBlockSize = (RoundBlockSizeMode::Margin === $qrCode->getRoundBlockSizeMode() ? 10 : intval($matrix->getBlockSize())) * self::QUALITY_MULTIPLIER;

        $baseImage = $this->createBaseImage($matrix, $baseBlockSize);
        $foregroundColor = $this->allocateForegroundColor($baseImage, $qrCode);

        $cornerImage = $this->prepareCornerImage($qrCode);
        $resizedCorner = $this->resizeCornerImage($cornerImage, $baseBlockSize);

        $this->drawMatrix($baseImage, $matrix, $baseBlockSize, $foregroundColor, $resizedCorner);

        imagedestroy($cornerImage);
        imagedestroy($resizedCorner);

        $targetImage = $this->createTargetImage($matrix, $qrCode, $label);

        $this->copyResampledImage(
            $targetImage,
            $baseImage,
            ['X' => $matrix->getMarginLeft(), 'Y' => $matrix->getMarginRight(), 'Width' => $matrix->getInnerSize(), 'Height' => $matrix->getInnerSize()],
            ['X' => 0, 'Y' => 0, 'Width' => imagesx($baseImage), 'Height' => imagesy($baseImage)]
        );

        imagedestroy($baseImage);

        $result = new GdResult($matrix, $targetImage);

        if ($logo instanceof LogoInterface) {
            $result = $this->addLogoToResult($logo, $result);
        }

        if ($label instanceof LabelInterface) {
            $result = $this->addLabelToResult($label, $result);
        }

        return $result;
    }

    /**
     * Creates the base image for the QR code
     *
     * @param MatrixInterface $matrix The matrix that defines the block layout for the QR code
     * @param int $baseBlockSize The size of each block in the QR code
     * 
     * @return GdImage The created base image resource
     */
    private function createBaseImage(
        MatrixInterface $matrix,
        int $baseBlockSize
    ): GdImage {
        $baseImage = imagecreatetruecolor(
            $matrix->getBlockCount() * $baseBlockSize,
            $matrix->getBlockCount() * $baseBlockSize
        );

        imageantialias($baseImage, true);
        imagesavealpha($baseImage, true);
        imagealphablending($baseImage, false);

        $transparentColor = imagecolorallocatealpha($baseImage, 0, 0, 0, 127);
        imagefill($baseImage, 0, 0, $transparentColor);

        return $baseImage;
    }

    /**
     * Allocates the foreground color for the base image
     *
     * @param GdImage $baseImage The base image to apply the foreground color to
     * @param QrCodeInterface $qrCode The QR code object to get the foreground color from
     * 
     * @return int The allocated color identifier
     */
    private function allocateForegroundColor(
        GdImage $baseImage,
        QrCodeInterface $qrCode
    ): int {
        return imagecolorallocatealpha(
            $baseImage,
            $qrCode->getForegroundColor()->getRed(),
            $qrCode->getForegroundColor()->getGreen(),
            $qrCode->getForegroundColor()->getBlue(),
            $qrCode->getForegroundColor()->getAlpha()
        );
    }

    /**
     * Prepares the corner image for the QR code
     *
     * @param QrCodeInterface $qrCode The QR code object
     * 
     * @return GdImage The prepared corner image
     * @throws \Exception If the corner image cannot be loaded
     */
    private function prepareCornerImage(
        QrCodeInterface $qrCode
    ): GdImage {
        $cornerImage = $this->imageOverlay->getImage();

        if (!$cornerImage) {
            throw new \Exception('Unable to load corner image');
        }

        imageantialias($cornerImage, true);
        imagesavealpha($cornerImage, true);

        return $this->tintImage($cornerImage, $qrCode->getForegroundColor());
    }

    /**
     * Resizes the corner image to fit the QR code matrix
     *
     * @param GdImage $cornerImage The original corner image
     * @param int $baseBlockSize The size of the base blocks in the QR code
     * 
     * @return GdImage The resized corner image
     */
    private function resizeCornerImage(
        GdImage $cornerImage,
        int $baseBlockSize
    ): GdImage {
        $cornerSize = self::FINDER_PATTERN_SIZE * $baseBlockSize;
        $resizedCorner = imagecreatetruecolor($cornerSize, $cornerSize);

        imageantialias($resizedCorner, true);
        imagesavealpha($resizedCorner, true);
        imagealphablending($resizedCorner, false);

        $transparent = imagecolorallocatealpha($resizedCorner, 0, 0, 0, 127);
        imagefill($resizedCorner, 0, 0, $transparent);

        imagealphablending($resizedCorner, true);

        $this->copyResampledImage(
            $resizedCorner,
            $cornerImage,
            ['X' => 0, 'Y' => 0, 'Width' => $cornerSize, 'Height' => $cornerSize,],
            ['X' => 0, 'Y' => 0, 'Width' => imagesy($cornerImage), 'Height' => imagesy($cornerImage)]
        );

        return $resizedCorner;
    }

    /**
     * Draws the entire QR code matrix onto the base image by iterating through each block and drawing filled blocks or corner blocks
     *
     * @param GdImage $baseImage The base image where the matrix will be drawn
     * @param MatrixInterface $matrix The matrix representing the QR code
     * @param int $baseBlockSize The size of each block in the QR code
     * @param int $foregroundColor The color to fill the blocks
     * @param GdImage $resizedCorner The resized corner image (used for corner blocks)
     * 
     * @return void
     */
    private function drawMatrix(
        GdImage $baseImage,
        MatrixInterface $matrix,
        int $baseBlockSize,
        int $foregroundColor,
        GdImage $resizedCorner
    ): void {
        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    $this->drawMatrixBlock($baseImage, $matrix, $rowIndex, $columnIndex, $baseBlockSize, $foregroundColor, $resizedCorner);
                }
            }
        }
    }

    /**
     * Draws a block of the matrix on the QR code image, handling special corner blocks and filling others with a foreground color
     *
     * @param GdImage $baseImage The base image where the block will be drawn
     * @param MatrixInterface $matrix The matrix representing the QR code
     * @param int $rowIndex The row index of the block
     * @param int $columnIndex The column index of the block
     * @param int $baseBlockSize The size of each block in the QR code
     * @param int $foregroundColor The color to fill the block
     * @param GdImage $resizedCorner The resized corner image (used for corner blocks)
     * 
     * @return void
     */
    private function drawMatrixBlock(
        GdImage $baseImage,
        MatrixInterface $matrix,
        int $rowIndex,
        int $columnIndex,
        int $baseBlockSize,
        int $foregroundColor,
        GdImage $resizedCorner
    ): void {
        $isTopLeft = $rowIndex < self::FINDER_PATTERN_SIZE && $columnIndex < self::FINDER_PATTERN_SIZE;
        $isTopRight = $rowIndex < self::FINDER_PATTERN_SIZE && $columnIndex >= $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE;
        $isBottomLeft = $rowIndex >= $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE && $columnIndex < self::FINDER_PATTERN_SIZE;

        if ($isTopLeft || $isTopRight || $isBottomLeft) {
            $this->drawCornerBlock($baseImage, $matrix, $rowIndex, $columnIndex, $baseBlockSize, $resizedCorner, $isTopLeft, $isTopRight, $isBottomLeft);
        } else {
            imagefilledrectangle(
                $baseImage,
                intval($columnIndex * $baseBlockSize),
                intval($rowIndex * $baseBlockSize),
                intval(($columnIndex + 1) * $baseBlockSize - 1),
                intval(($rowIndex + 1) * $baseBlockSize - 1),
                $foregroundColor
            );
        }
    }

    /**
     * Draws a corner block on the QR code image (top-left, top-right, or bottom-left)
     *
     * @param GdImage $baseImage The base image where the corner block will be drawn
     * @param MatrixInterface $matrix The matrix representing the QR code
     * @param int $rowIndex The row index of the block
     * @param int $columnIndex The column index of the block
     * @param int $baseBlockSize The size of each block in the QR code
     * @param GdImage $resizedCorner The resized corner image
     * @param bool $isTopLeft Whether the corner is top-left
     * @param bool $isTopRight Whether the corner is top-right
     * @param bool $isBottomLeft Whether the corner is bottom-left
     * 
     * @return void
     */
    private function drawCornerBlock(
        GdImage $baseImage,
        MatrixInterface $matrix,
        int $rowIndex,
        int $columnIndex,
        int $baseBlockSize,
        GdImage $resizedCorner,
        bool $isTopLeft,
        bool $isTopRight,
        bool $isBottomLeft
    ): void {
        if (($isTopLeft && $rowIndex === 0 && $columnIndex === 0) ||
            ($isTopRight && $rowIndex === 0 && $columnIndex === $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE) ||
            ($isBottomLeft && $rowIndex === $matrix->getBlockCount() - self::FINDER_PATTERN_SIZE && $columnIndex === 0)
        ) {

            $rotatedCorner = $resizedCorner;

            if ($isTopRight) {
                $rotatedCorner = imagerotate($resizedCorner, 270, imagecolorallocatealpha($resizedCorner, 0, 0, 0, 127));
            } elseif ($isBottomLeft) {
                $rotatedCorner = imagerotate($resizedCorner, 90, imagecolorallocatealpha($resizedCorner, 0, 0, 0, 127));
            }

            $this->copyResampledImage(
                $baseImage,
                $rotatedCorner,
                ['X' => intval($columnIndex * $baseBlockSize), 'Y' => intval($rowIndex * $baseBlockSize), 'Width' => self::FINDER_PATTERN_SIZE * $baseBlockSize, 'Height' => self::FINDER_PATTERN_SIZE * $baseBlockSize,],
                ['X' => 0, 'Y' => 0, 'Width' => self::FINDER_PATTERN_SIZE * $baseBlockSize, 'Height' => self::FINDER_PATTERN_SIZE * $baseBlockSize,]
            );
        }
    }

    /**
     * Creates the target image for the QR code, including label if provided
     *
     * @param MatrixInterface $matrix The matrix representation of the QR code
     * @param QrCodeInterface $qrCode The QR code instance
     * @param LabelInterface|null $label The optional label to add below the QR code
     * 
     * @return resource|GdImage The created target image.
     */
    private function createTargetImage(
        MatrixInterface $matrix,
        QrCodeInterface $qrCode,
        ?LabelInterface $label
    ): GdImage {
        $targetWidth = $matrix->getOuterSize();
        $targetHeight = $matrix->getOuterSize();

        if ($label instanceof LabelInterface) {
            $labelImageData = LabelImageData::createForLabel($label);
            $targetHeight += $labelImageData->getHeight() + $label->getMargin()->getTop() + $label->getMargin()->getBottom();
        }

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imageantialias($targetImage, true);
        imagesavealpha($targetImage, true);
        imagealphablending($targetImage, false);

        $backgroundColor = imagecolorallocatealpha(
            $targetImage,
            $qrCode->getBackgroundColor()->getRed(),
            $qrCode->getBackgroundColor()->getGreen(),
            $qrCode->getBackgroundColor()->getBlue(),
            $qrCode->getBackgroundColor()->getAlpha()
        );

        imagefill($targetImage, 0, 0, $backgroundColor);
        imagealphablending($targetImage, true);

        return $targetImage;
    }

    /**
     * Resamples and copies a portion of the source image to the destination image
     *
     * @param GdImage $dstImage Destination image resource
     * @param GdImage $srcImage Source image resource
     * @param array $dst_X_Y_W_H Destination coordinates and dimensions (X, Y, Width, Height)
     * @param array $src_X_Y_W_H Source coordinates and dimensions (X, Y, Width, Height)
     * 
     * @return bool True on success, false on failure
     */
    private function copyResampledImage(
        GdImage $dstImage,
        GdImage $srcImage,
        array $dst_X_Y_W_H,
        array $src_X_Y_W_H
    ): bool {
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
     * Tints the image with a specified color, applying the color to non-transparent pixels
     * 
     * @param GdImage $image The image to tint
     * @param ColorInterface $color The color to apply to the image
     * 
     * @return GdImage The tinted image
     */
    private function tintImage(
        GdImage $image,
        ColorInterface $color
    ): GdImage {
        $width = imagesx($image);
        $height = imagesy($image);

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
     * Adds a logo to the image, centered and with optional punchout background
     * Throws an exception if the logo is not in PNG format
     * 
     * @param LogoInterface $logo The logo to add
     * @param GdResult $result The image to add the logo to
     * 
     * @return GdResult The updated image with the logo
     */
    private function addLogoToResult(
        LogoInterface $logo,
        GdResult $result
    ): GdResult {
        $logoImageData = LogoImageData::createForLogo($logo);

        if ('image/png' !== $logoImageData->getMimeType()) {
            throw new \Exception('PNG Writer does not support SVG logo');
        }

        $targetImage = $result->getImage();
        $matrix = $result->getMatrix();

        if ($logoImageData->getPunchoutBackground()) {
            $transparent = imagecolorallocatealpha($targetImage, 255, 255, 255, 127);
            imagealphablending($targetImage, false);
            $xOffsetStart = intval($matrix->getOuterSize() / 2 - $logoImageData->getWidth() / 2);
            $yOffsetStart = intval($matrix->getOuterSize() / 2 - $logoImageData->getHeight() / 2);
            for ($xOffset = $xOffsetStart; $xOffset < $xOffsetStart + $logoImageData->getWidth(); ++$xOffset) {
                for ($yOffset = $yOffsetStart; $yOffset < $yOffsetStart + $logoImageData->getHeight(); ++$yOffset) {
                    imagesetpixel($targetImage, $xOffset, $yOffset, $transparent);
                }
            }
        }

        $this->copyResampledImage(
            $targetImage,
            $logoImageData->getImage(),
            ['X' => intval($matrix->getOuterSize() / 2 - $logoImageData->getWidth() / 2), 'Y' => intval($matrix->getOuterSize() / 2 - $logoImageData->getWidth() / 2), 'Width' => $logoImageData->getWidth(), 'Height' => $logoImageData->getHeight()],
            ['X' => 0, 'Y' => 0, 'Width' => imagesy($logoImageData->getImage()), 'Height' => imagesy($logoImageData->getImage())]
        );

        return new GdResult($matrix, $targetImage);
    }


    /**
     * Adds a label with text to the image
     *
     * The label's position is based on its alignment (left, center, right) and margin
     * The text is drawn using the specified font and color
     * 
     * @param LabelInterface $label The label to add
     * @param GdResult $result The image to add the label to
     * 
     * @return GdResult The updated image
     */
    private function addLabelToResult(
        LabelInterface $label,
        GdResult $result
    ): GdResult {
        $targetImage = $result->getImage();
        $labelImageData = LabelImageData::createForLabel($label);

        $textColor = imagecolorallocatealpha(
            $targetImage,
            $label->getTextColor()->getRed(),
            $label->getTextColor()->getGreen(),
            $label->getTextColor()->getBlue(),
            $label->getTextColor()->getAlpha()
        );

        $x = intval(imagesx($targetImage) / 2 - $labelImageData->getWidth() / 2);
        $y = imagesy($targetImage) - $label->getMargin()->getBottom();

        if (LabelAlignment::Left === $label->getAlignment()) {
            $x = $label->getMargin()->getLeft();
        } elseif (LabelAlignment::Right === $label->getAlignment()) {
            $x = imagesx($targetImage) - $labelImageData->getWidth() - $label->getMargin()->getRight();
        }

        imagettftext($targetImage, $label->getFont()->getSize(), 0, $x, $y, $textColor, $label->getFont()->getPath(), $label->getText());

        return new GdResult($result->getMatrix(), $targetImage);
    }
}
