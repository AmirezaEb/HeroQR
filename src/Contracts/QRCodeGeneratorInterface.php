<?php

namespace HeroQR\Contracts;

use InvalidArgumentException;

/**
 * Interface QRCodeGeneratorInterface
 * Defines the contract for generating customizable QR codes.
 */
interface QRCodeGeneratorInterface
{
    /**
     * Generate a QR code in the specified format.
     *
     * @param string $format The desired output format (e.g., 'png', 'svg').
     * @return string The data URI of the generated QR code.
     * @throws InvalidArgumentException If the format is invalid.
     */
    public function generate(string $format): string;

    /**
     * Set the data for the QR code.
     *
     * @param string $data The data to encode.
     * @return self
     * @throws InvalidArgumentException If the data is empty.
     */
    public function setData(string $data): self;

    /**
     * Set the size of the QR code.
     *
     * @param int $size The size in pixels.
     * @return self
     * @throws InvalidArgumentException If the size is not positive.
     */
    public function setSize(int $size): self;

    /**
     * Set the margin for the QR code.
     *
     * @param int $margin The margin in pixels.
     * @return self
     * @throws InvalidArgumentException If the margin is negative.
     */
    public function setMargin(int $margin): self;

    /**
     * Set the foreground color of the QR code.
     *
     * @param string $hexColor The hex color code.
     * @return self
     */
    public function setColor(string $hexColor): self;

    /**
     * Set the background color of the QR code.
     *
     * @param string $hexColor The hex color code.
     * @return self
     */
    public function setBackgroundColor(string $hexColor): self;

    /**
     * Set the logo for the QR code.
     *
     * @param string $logoPath Path to the logo image.
     * @param int $logoSize Width of the logo in pixels.
     * @return self
     * @throws InvalidArgumentException If the logo path is invalid.
     */
    public function setLogo(string $logoPath, int $logoSize): self;

    /**
     * Set the label for the QR code.
     *
     * @param string $label The label text.
     * @param string $textAlign Text alignment ('left', 'center', 'right').
     * @param string $textColor Hex color for the text.
     * @param int $fontSize Font size in points.
     * @param array $margin Margin around the label [top, right, bottom, left].
     * @return self
     */
    public function setLabel(
        string $label,
        string $textAlign,
        string $textColor,
        int $fontSize,
        array $margin
    ): self;

    /**
     * Set the encoding for the QR code data.
     *
     * @param string $encoding The encoding format (e.g., 'UTF-8').
     * @return self
     */
    public function setEncoding(string $encoding): self;
}
