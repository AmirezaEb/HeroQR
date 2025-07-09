<?php

namespace HeroQR\Core;

use Endroid\QrCode\{Builder\Builder, Matrix\Matrix};
use Endroid\QrCode\{Color\ColorInterface, ErrorCorrectionLevel, Exception\ValidationException, RoundBlockSizeMode};
use Endroid\QrCode\Writer\{Result\ResultInterface, WriterInterface};
use HeroQR\{Contracts\QRCodeGeneratorInterface, DataTypes\DataType};
use HeroQR\Managers\{ColorManager, EncodingManager, LabelManager, LogoManager, OutputManager, WriterManager};

/**
 * Handles the generation of QR codes with customizable options.
 *
 * Provides functionality for generating QR codes in various formats
 * (PNG, SVG, PDF and More) with optional customization options such as shapes,
 * markers, and internal patterns. Ensures flexibility and ease of use
 * for both standard and advanced use cases.
 *
 * @package HeroQR/Core
 */
class QRCodeGenerator implements QrCodeGeneratorInterface
{
    private ?ResultInterface $builder = null;
    private LabelManager $labelManager;
    private bool $roundBlockSizeModeExplicitly;

    /**
     * QRCodeGenerator constructor
     * Initializes the necessary services and sets default values for QR code properties
     *
     * @param string $outputFormat The output format (default is 'getDataUri')
     * @param int $size The size of the QR code (default is 800)
     * @param int $margin The margin around the QR code (default is 10)
     * @param LogoManager $logoManager The logo manager instance
     * @param ColorManager $colorManager The color manager instance
     * @param EncodingManager $encodingManager The encoding manager instance
     */
    public function __construct(
        private int                      $size = 800,
        private int                      $margin = 10,
        private string                   $data = '',
        private readonly string          $outputFormat = 'getDataUri',
        private readonly LogoManager     $logoManager = new LogoManager(),
        private readonly ColorManager    $colorManager = new ColorManager(),
        private readonly WriterManager   $writerManager = new WriterManager(),
        private readonly OutputManager   $outputManager = new OutputManager(),
        private readonly EncodingManager $encodingManager = new EncodingManager(),
        private RoundBlockSizeMode $roundBlockSizeMode = RoundBlockSizeMode::Margin,
        private ErrorCorrectionLevel $errorCorrectionLevel = ErrorCorrectionLevel::High
    )
    {
        $this->labelManager = new LabelManager($this->colorManager);
    }

    /**
     * Magic method to return the QR code as a string, based on the specified output format
     *
     * @return string The generated QR code as a string
     */
    public function __toString(): string
    {
        return $this->outputFormat === 'getDataUri' ? $this->getDataUri() : $this->getString();
    }

    /**
     * Generates a QR code in the specified format with optional customization.
     *
     * @param string $format The desired format for the QR code ('gif', 'png', 'svg', 'webp', 'eps', 'pdf', 'binary').
     * @param array $customs Optional customization parameters for the QR code. If provided, they must follow this structure:
     * - 'Shape' ('S1' to 'S4'): Defines the overall shape of the QR code.
     * - 'Marker' ('M1' to 'M4'): Specifies the corner marker styles.
     * - 'Cursor' ('C1' to 'C4'): Adjusts the internal patterns.
     * - Example:
     * - generate('png', [
     *  'Shape' => 'S4',
     *  'Marker' => 'M4',
     *  'Cursor' => 'C4'
     *  ]);
     *
     * @return self The current QRCodeGenerator instance for method chaining.
     * @throws \InvalidArgumentException|ValidationException If the specified format is not supported.
     */
    public function generate(string $format = 'png', array $customs = []): self
    {
        $this->builder = (new Builder(
            writer: $this->validateWriter($format, $customs),
            writerOptions: [],
            validateResult: false,
            data: $this->getData(),
            encoding: $this->encodingManager->getEncoding(),
            errorCorrectionLevel: $this->getErrorCorrectionLevel(),
            size: $this->getSize(),
            margin: $this->getMargin(),
            roundBlockSizeMode: $this->getBlockSizeMode(),
            foregroundColor: $this->colorManager->getColor(),
            backgroundColor: $this->colorManager->getBackgroundColor(),
            labelText: $this->labelManager->getLabel(),
            labelFont: $this->labelManager->getLabelFont(),
            labelAlignment: $this->labelManager->getLabelAlign(),
            labelMargin: $this->labelManager->getLabelMargin(),
            labelTextColor: $this->labelManager->getLabelColor(),
            logoPath: $this->logoManager->getLogoPath(),
            logoResizeToWidth: $this->logoManager->getLogoSize(),
            logoResizeToHeight: $this->logoManager->getLogoSize(),
            logoPunchoutBackground: $this->logoManager->getLogoBackground(),
        ))->build();

        return $this;
    }

    /**
     * Returns the QR code's matrix representation
     * The matrix is a grid of black and white cells representing the QR code
     *
     * @return Matrix The matrix representation of the QR code
     * @throws \RuntimeException If the QR code has not been generated yet
     */
    public function getMatrix(): Matrix
    {
        $this->ensureBuilderExists();
        return $this->outputManager->getMatrix($this->builder);
    }

    /**
     * Get the matrix as an array
     *
     * @return array The QR code matrix represented as a 2D array
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function getMatrixAsArray(): array
    {
        $this->ensureBuilderExists();
        return $this->outputManager->getMatrixAsArray($this->builder);
    }

    /**
     * Returns the QR code as a raw string
     *
     * @return string The raw string representation of the QR code
     * @throws \RuntimeException If the QR code has not been generated yet
     */
    public function getString(): string
    {
        $this->ensureBuilderExists();
        return $this->outputManager->getString($this->builder);
    }

    /**
     * Returns the QR code as a Base64-encoded data URI
     *
     * @return string The data URI representation of the QR code
     * @throws \RuntimeException If the QR code has not been generated yet
     */
    public function getDataUri(): string
    {
        $this->ensureBuilderExists();
        return $this->outputManager->getDataUri($this->builder);
    }

    /**
     * Save the generated QR code to a file
     *
     * @param string $path The path to save the QR code file
     * @return bool True if the file was saved successfully, false otherwise
     * @throws \InvalidArgumentException If the format is unsupported
     * @throws \RuntimeException If no QR code has been generated yet
     */
    public function saveTo(string $path): bool
    {
        $this->ensureBuilderExists();
        return $this->outputManager->saveTo($this->builder, $path);
    }

    /**
     * Set the data to be encoded in the QR code
     *
     * @param string $data The data to encode
     * @param DataType $type DataType auto validation (default = DataType::Text)
     * @return self
     */
    public function setData(string $data, DataType $type = DataType::Text): self
    {
        $class = $type->value;

        if (!$class::validate($data)) {
            throw new \InvalidArgumentException("Invalid data for type: " . $class::getType());
        }

        if (empty(trim($data))) {
            throw new \InvalidArgumentException('Data cannot be empty.');
        }

        $this->data = $this->dataSanitizer($data, $type);
        return $this;
    }

    /**
     * Get the data value
     *
     * @return string|null The data if available, null otherwise
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * Set the size of the QR code
     *
     * @param int $size The size of the QR code
     * @return self
     * @throws \InvalidArgumentException If the size is not a positive integer
     */
    public function setSize(int $size): self
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Size must be a positive integer.');
        }

        $this->size = $size;
        return $this;
    }

    /**
     * Get the size value
     *
     * @return int The size value
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set the margin around the QR code
     *
     * @param int $margin The margin size
     * @return self
     * @throws \InvalidArgumentException If the margin is negative
     */
    public function setMargin(int $margin): self
    {
        if ($margin < 0) {
            throw new \InvalidArgumentException('Margin cannot be negative.');
        }

        $this->margin = $margin;
        return $this;
    }

    /**
     * Get the margin value
     *
     * @return int The margin value
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * Set the round block size mode
     *
     * The string can be either:
     * - The enum case name (e.g. "None", "Margin", "Enlarge", "Shrink") — case-insensitive.
     *
     * @param string $mode The round block size mode as a string.
     * @return self
     * @throws \InvalidArgumentException If the given mode is invalid.
     */
    public function setBlockSizeMode(string $mode): self
    {
        $mode = strtolower($mode);

        $validModes = array_merge(
            array_map('strtolower', array_column(RoundBlockSizeMode::cases(), 'name')),
            array_map('strtolower', array_column(RoundBlockSizeMode::cases(), 'value'))
        );

        if (!in_array($mode, $validModes, true)) {
            throw new \InvalidArgumentException(
                'Invalid round block size mode. Accepted values: ' . implode(', ', array_unique($validModes)) . '.'
            );
        }

        foreach (RoundBlockSizeMode::cases() as $case) {
            if (strtolower($case->name) === $mode || strtolower($case->value) === $mode) {
                $this->roundBlockSizeMode = $case;
                break;
            }
        }

        return $this;
    }

    /**
     * Get the current round block size mode of the QR code.
     *
     * @return RoundBlockSizeMode The current block size mode enum instance.
     */
    public function getBlockSizeMode(): RoundBlockSizeMode
    {
        return $this->roundBlockSizeMode;
    }

    /**
     * Set the error correction level for the QR code
     *
     * The string can be either:
     * - The enum case name (e.g. "Low", "Medium", "Quartile", "High") — case-insensitive.
     *
     * @param string $level The error correction level as a string.
     * @return self
     * @throws \InvalidArgumentException If the given level is invalid. Accepted values: low, medium, quartile, high.
     */
    public function setErrorCorrectionLevel(string $level): self
    {
        $level = strtolower($level);

        $validLevels = array_merge(
            array_map('strtolower', array_column(ErrorCorrectionLevel::cases(), 'name')),
            array_map('strtolower', array_column(ErrorCorrectionLevel::cases(), 'value'))
        );

        if (!in_array($level, $validLevels, true)) {
            throw new \InvalidArgumentException(
                'Invalid error correction level. Accepted values: ' . implode(', ', array_unique($validLevels)) . '.'
            );
        }

        foreach (ErrorCorrectionLevel::cases() as $case) {
            if (strtolower($case->name) === $level || strtolower($case->value) === $level) {
                $this->errorCorrectionLevel = $case;
                break;
            }
        }

        return $this;
    }

    /**
     * Get the current error correction level of the QR code
     *
     * @return ErrorCorrectionLevel The current error correction level enum instance.
     */
    public function getErrorCorrectionLevel(): ErrorCorrectionLevel
    {
        return $this->errorCorrectionLevel;
    }

    /**
     * Set the color of the QR code foreground
     *
     * @param string $hexColor The hexadecimal color code
     * @return self
     * @throws \InvalidArgumentException If the color format is invalid
     */
    public function setColor(string $hexColor): self
    {
        $this->colorManager->setColor($hexColor);
        return $this;
    }

    /**
     * Get the color from the color manager
     *
     * @return ColorInterface The color object
     */
    public function getColor(): ColorInterface
    {
        return $this->colorManager->getColor();
    }

    /**
     * Set the background color of the QR code
     *
     * @param string $hexColor The hexadecimal color code
     * @return self
     * @throws \InvalidArgumentException If the color format is invalid
     */
    public function setBackgroundColor(string $hexColor): self
    {
        $this->colorManager->setBackgroundColor($hexColor);
        return $this;
    }

    /**
     * Get the background color from the color manager
     *
     * @return ColorInterface The background color object
     */
    public function getBackgroundColor(): ColorInterface
    {
        return $this->colorManager->getBackgroundColor();
    }

    /**
     * Set the logo to be embedded in the QR code
     *
     * @param string $logoPath The path to the logo file
     * @param int $logoSize The size of the logo (default is 80)
     * @return self
     * @throws \InvalidArgumentException If the logo file does not exist
     */
    public function setLogo(string $logoPath, int $logoSize = 80): self
    {
        if (!file_exists($logoPath)) {
            throw new \InvalidArgumentException('Logo File Does Not Exist');
        }

        $this->logoManager->setLogo($logoPath);
        $this->logoManager->setLogoSize($logoSize);
        return $this;
    }

    /**
     * Get the logo path from the logo manager
     *
     * @return string|null The logo path if available, null otherwise
     */
    public function getLogoPath(): ?string
    {
        return $this->logoManager->getLogoPath();
    }

    /**
     * Set the label properties for the QR code
     *
     * @param string $label The text label to be displayed on the QR code
     * @param string $textAlign The text alignment for the label (default is 'center')
     * @param string $textColor The color of the label text in hexadecimal format (default is '#000000')
     * @param int $fontSize The font size of the label text (default is 50)
     * @param array $margin The margin around the label in the format [top, right, bottom, left] (default is [0, 10, 10, 10])
     * @return self Returns the current instance for method chaining
     * @throws \InvalidArgumentException If the label text is empty
     */
    public function setLabel(
        string $label,
        string $textAlign = 'center',
        string $textColor = '#000000',
        int    $fontSize = 50,
        array  $margin = [0, 10, 10, 10]
    ): self
    {
        if (empty($label)) {
            throw new \InvalidArgumentException('Label cannot be empty');
        }

        $this->labelManager->setLabel($label);
        $this->labelManager->setLabelAlign($textAlign);
        $this->labelManager->setLabelColor($textColor);
        $this->labelManager->setLabelSize($fontSize);
        $this->labelManager->setLabelMargin($margin);

        return $this;
    }

    /**
     * Get the label from the label manager
     *
     * @return string|null The label if available, null otherwise
     */
    public function getLabel(): ?string
    {
        return $this->labelManager->getLabel();
    }

    /**
     * Set the encoding type for the QR code
     *
     * @param string $encoding The encoding type ('UTF-16' ,'UTF-8', 'ASCII', 'ISO-8859-1', 'ISO-8859-5', 'ISO-8859-15') and more...
     * @return self Returns the current instance for method chaining
     * @throws \Exception
     */
    public function setEncoding(string $encoding): self
    {
        $this->encodingManager->setEncoding($encoding);

        return $this;
    }

    /**
     * Encodes the data with type-specific rules and sanitizes the input
     * Supports data types like Email, Phone, and Location
     *
     * @param string $data The raw data to encode
     * @param DataType $type The type of data being encoded (Url, WiFi, Location, Text, Email, Phone)
     * @return string Sanitized and properly formatted data string
     */
    private function dataSanitizer(string $data, DataType $type): string
    {
        $data = htmlspecialchars($data);

        return match ($type) {
            DataType::Email => "mailto:{$data}",
            DataType::Phone => "tel:{$data}",
            DataType::Wifi => "$data",
            DataType::Location => "https://www.google.com/maps?q=$data",
            default => $data,
        };
    }

    /**
     * Validates the writer format and returns an appropriate WriterInterface instance
     * Supports both standard and custom writer formats
     *
     * @param string $format The format of the writer ("svg", "png", "pdf",and more...)
     * @param array $customs An optional array of custom values for M Or C Or S
     * @return WriterInterface
     * @throws \InvalidArgumentException|\RuntimeException
     */
    private function validateWriter(string $format, array $customs): WriterInterface
    {
        return $this->writerManager->getWriter($format, $customs);
    }

    /**
     * Helper method to ensure that the builder has been initialized
     *
     * @throws \RuntimeException If the builder has not been initialized
     */
    private function ensureBuilderExists(): void
    {
        if ($this->builder === null) {
            throw new \Error('No QR Code has been generated. Call generate() first.');
        }
    }
}
