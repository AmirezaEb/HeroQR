<?php

namespace HeroQR\Contracts;

use Endroid\QrCode\Matrix\Matrix;
use HeroQR\DataTypes\DataType;

/**
 * Interface QRCodeGeneratorInterface
 *
 * This interface defines methods for generating and customizing QR codes, including
 * setting data, size, color, margin, logo, label, and encoding. It also provides
 * methods for generating the QR code in various formats, retrieving the matrix
 * representation, and saving the QR code to a file.
 *
 * @package HeroQR\Contracts
 */
interface QRCodeGeneratorInterface
{
    /**
     * Generate a QR code in the specified format
     *
     * @param string $format The desired output format (e.g., 'png', 'svg')
     * @return self
     * @throws \InvalidArgumentException If the format is invalid
     */
    public function generate(string $format): self;

    /**
     * Get the matrix representation of the QR code
     *
     * @return Matrix The matrix object representing the QR code
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function getMatrix(): Matrix;

    /**
     * Get the matrix as an array
     *
     * @return array The QR code matrix represented as a 2D array
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function getMatrixAsArray(): array;

    /**
     * Get the QR code as a string
     *
     * @return string The QR code as a string
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function getString(): string;

    /**
     * Get the QR code as a Data URI
     *
     * @return string The QR code as a Data URI
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function getDataUri(): string;

    /**
     * Save the generated QR code to a file
     *
     * @param string $path The path to save the QR code file
     * @return bool True if the file was saved successfully, false otherwise
     * @throws \InvalidArgumentException If the format is unsupported
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function saveTo(string $path): bool;

    /**
     * Set the data to be encoded in the QR code
     *
     * @param string $data The data to encode
     * @param DataType $type The type of the data (e.g., Email, Phone, WiFi, Location)
     * @return self
     * @throws \InvalidArgumentException If the data is empty or invalid
     */
    public function setData(string $data, DataType $type): self;

    /**
     * Set the size of the QR code
     *
     * @param int $size The size of the QR code
     * @return self
     * @throws \InvalidArgumentException If the size is not a positive integer
     */
    public function setSize(int $size): self;

    /**
     * Set the margin around the QR code
     *
     * @param int $margin The margin size
     * @return self
     * @throws \InvalidArgumentException If the margin is negative
     */
    public function setMargin(int $margin): self;

    /**
     * Set the round block size mode
     *
     * @param string $mode The round block size mode as a string.
     * @return self
     * @throws \InvalidArgumentException If the given mode is invalid.
     */
    public function setBlockSizeMode(string $mode): self;

    /**
     * Set the error correction level for the QR code
     *
     * @param string $level The error correction level as a string.
     * @return self
     * @throws \InvalidArgumentException If the given level is invalid.
     */
    public function setErrorCorrectionLevel(string $level): self;

    /**
     * Set the color of the QR code foreground
     *
     * @param string $hexColor The hexadecimal color code
     * @return self
     * @throws \InvalidArgumentException If the color format is invalid
     */
    public function setColor(string $hexColor): self;

    /**
     * Set the background color of the QR code
     *
     * @param string $hexColor The hexadecimal color code
     * @return self
     * @throws \InvalidArgumentException If the color format is invalid
     */
    public function setBackgroundColor(string $hexColor): self;

    /**
     * Set the logo to be embedded in the QR code
     *
     * @param string $logoPath The path to the logo file
     * @param int $logoSize The size of the logo
     * @return self
     * @throws \InvalidArgumentException If the logo file does not exist
     */
    public function setLogo(string $logoPath, int $logoSize = 40): self;

    /**
     * Set the label for the QR code
     *
     * @param string $label The label text
     * @param string $textAlign The text alignment (e.g., 'center', 'left')
     * @param string $textColor The text color in hex format
     * @param int $fontSize The font size of the label
     * @param array $margin The margin for the label [top, right, bottom, left]
     * @return self
     * @throws \InvalidArgumentException If the label is empty
     */
    public function setLabel(
        string $label,
        string $textAlign = 'center',
        string $textColor = '#000000',
        int    $fontSize = 20,
        array  $margin = [0, 10, 10, 10]
    ): self;

    /**
     * Set the encoding for the QR code
     *
     * @param string $encoding The encoding type
     * @return self
     */
    public function setEncoding(string $encoding): self;
}
