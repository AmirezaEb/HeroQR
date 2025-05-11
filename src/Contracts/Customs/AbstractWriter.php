<?php

declare(strict_types=1);

namespace HeroQR\Contracts\Customs;

use Endroid\QrCode\{QrCodeInterface, RoundBlockSizeMode};
use Endroid\QrCode\{Logo\LogoInterface, Matrix\MatrixInterface};
use Endroid\QrCode\ImageData\{LabelImageData, LogoImageData};
use Endroid\QrCode\Label\{LabelAlignment, LabelInterface};
use Endroid\QrCode\Writer\{WriterInterface, AbstractGdWriter};
use Endroid\QrCode\Writer\Result\{GdResult, ResultInterface};
use HeroQR\Customs\{ImageOverlay, ShapePaths};

/**
 * The AbstractWriter class is responsible for generating QR code images using the GD library
 * This class provides base functionality for creating QR code images with options like logo and label embedding
 * Subclasses should implement specific logic for rendering QR codes using the GD image processing library
 *
 * @package HeroQR\Contracts\Customs
 */
readonly abstract class AbstractWriter extends AbstractGdWriter implements WriterInterface
{
    private const QUALITY_MULTIPLIER = 10;
    private ImageOverlay $imageOverlay;
    protected string $blockShape;

    public function __construct($background, $overlay, $blockShape)
    {
        $this->imageOverlay = new ImageOverlay($background, $overlay);
        $this->blockShape = ShapePaths::getValueByKey($blockShape) ?? 'drawSquare';
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
        ?LogoInterface  $logo = null,
        ?LabelInterface $label = null,
        array           $options = []
    ): ResultInterface {
        if (!extension_loaded('gd')) {
            throw new \Exception('Unable to generate image: please check if the GD extension is enabled and configured correctly');
        }

        $matrix = $this->getMatrix($qrCode);
        $baseBlockSize = (RoundBlockSizeMode::Margin === $qrCode->getRoundBlockSizeMode() ? 10 : intval($matrix->getBlockSize())) * self::QUALITY_MULTIPLIER;

        $baseImage = $this->createBaseImage($matrix, $baseBlockSize);
        $foregroundColor = $this->allocateForegroundColor($baseImage, $qrCode);

        Drawer::drawQrCode($baseImage, $matrix, $baseBlockSize, $foregroundColor, $qrCode, $this->blockShape, $this->imageOverlay);

        $targetImage = $this->createTargetImage($matrix, $qrCode, $label);

        Drawer::copyResampledImage(
            $targetImage,
            $baseImage,
            ['X' => $matrix->getMarginLeft(), 'Y' => $matrix->getMarginRight(), 'Width' => $matrix->getInnerSize(), 'Height' => $matrix->getInnerSize()],
            ['X' => 0, 'Y' => 0, 'Width' => imagesy($baseImage), 'Height' => imagesy($baseImage)]
        );

        $this->destroyImages([$baseImage]);

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
     * @return \GdImage The created base image resource
     */
    private function createBaseImage(
        MatrixInterface $matrix,
        int             $baseBlockSize
    ): \GdImage {
        $baseImage = imagecreatetruecolor($matrix->getBlockCount() * $baseBlockSize, $matrix->getBlockCount() * $baseBlockSize);

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
     * @param \GdImage $baseImage The base image to apply the foreground color to
     * @param QrCodeInterface $qrCode The QR code object to get the foreground color from
     *
     * @return int The allocated color identifier
     */
    private function allocateForegroundColor(
        \GdImage        $baseImage,
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
     * Creates the target image for the QR code, including label if provided
     *
     * @param MatrixInterface $matrix The matrix representation of the QR code
     * @param QrCodeInterface $qrCode The QR code instance
     * @param LabelInterface|null $label The optional label to add below the QR code
     *
     * @return resource|\GdImage The created target image.
     * @throws \Exception
     */
    private function createTargetImage(
        MatrixInterface $matrix,
        QrCodeInterface $qrCode,
        ?LabelInterface $label
    ): \GdImage {
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
            (int)round($qrCode->getBackgroundColor()->getAlpha() * 127 / 255)
        );

        imagefill($targetImage, 0, 0, $backgroundColor);
        imagealphablending($targetImage, true);

        return $targetImage;
    }

    /**
     * Adds a logo to the image, centered and with optional punch out background
     * Throws an exception if the logo is not in PNG format
     *
     * @param LogoInterface $logo The logo to add
     * @param GdResult $result The image to add the logo to
     *
     * @return GdResult The updated image with the logo
     * @throws \Exception
     */
    private function addLogoToResult(
        LogoInterface $logo,
        GdResult      $result
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

        Drawer::copyResampledImage(
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
     * @throws \Exception
     */
    private function addLabelToResult(
        LabelInterface $label,
        GdResult       $result
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

    /**
     * Frees up memory by destroying image resources.
     *
     * @param array $images An array of image resources.
     * @return void
     */
    private function destroyImages(array $images): void
    {
        foreach ($images as $image) {
            imagedestroy($image);
        }
    }
}