<?php

namespace HeroQR\Core\Writers;

use HeroQR\Contracts\Customs\AbstractWriter;
use Endroid\QrCode\{Label\LabelInterface,Logo\LogoInterface, QrCodeInterface};
use Endroid\QrCode\Writer\Result\{GdResult, PngResult, ResultInterface};

/**
 * Custom PNG Writer for generating QR codes with optional compression level
 * Allows for customized QR code generation with flexible compression settings
 *
 * @package HeroQR\Core\Writers
 */
readonly class CustomPngWriter extends AbstractWriter
{
    public const WRITER_OPTION_COMPRESSION_LEVEL = 'compression_level';

    /**
     * Generates a PNG result from a QR code, with optional logo and label
     *
     * @param QrCodeInterface $qrCode QR Code instance to render
     * @param LogoInterface|null $logo Optional logo to embed in the QR code
     * @param LabelInterface|null $label Optional label to add to the QR code
     * @param array $options Writer options (compression level and...)
     * @return ResultInterface The resulting PNG QR code
     * @throws \Exception
     */
    public function write(
        QrCodeInterface $qrCode,
        ?LogoInterface  $logo = null,
        ?LabelInterface $label = null,
        array           $options = []
    ): ResultInterface{

        $options[self::WRITER_OPTION_COMPRESSION_LEVEL] = $options[self::WRITER_OPTION_COMPRESSION_LEVEL] ?? 1;

        /**
         * @var GdResult $gdResult
         */
        $gdResult = parent::write($qrCode, $logo, $label, $options);

        return new PngResult(
            $gdResult->getMatrix(),
            $gdResult->getImage(),
            $options[self::WRITER_OPTION_COMPRESSION_LEVEL]
        );
    }
}
