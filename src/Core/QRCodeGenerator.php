<?php

namespace HeroQR\Core;

use RuntimeException;
use InvalidArgumentException;
use HeroQR\DataTypes\DataType;
use HeroQR\Managers\LogoManager;
use Endroid\QrCode\Matrix\Matrix;
use HeroQR\Managers\LabelManager;
use HeroQR\Managers\ColorManager;
use HeroQR\Managers\OutputManager;
use Endroid\QrCode\Builder\Builder;
use HeroQR\Managers\EncodingManager;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Writer\WriterInterface;
use HeroQR\Contracts\QRCodeGeneratorInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;

/**
 * Class QRCodeGenerator
 * Handles the generation of QR codes with customizable options
 */
class QRCodeGenerator implements QrCodeGeneratorInterface
{
    private ResultInterface $builder;
    private LabelManager $labelManager;

    /**
     * QRCodeGenerator constructor.
     * Initializes the necessary services and sets default values for QR code properties
     *
     * @param string $outputFormat The output format (default is 'getDataUri')
     * @param int $size The size of the QR code (default is 300)
     * @param int $margin The margin around the QR code (default is 10)
     * @param LogoManager $logoManager The logo manager instance
     * @param ColorManager $colorManager The color manager instance
     * @param EncodingManager $encodingManager The encoding manager instance
     */
    public function __construct(
        private int $size = 300,
        private int $margin = 10,
        private string $data = '',
        private string $outputFormat = 'getDataUri',
        private LogoManager $logoManager = new LogoManager(),
        private ColorManager $colorManager = new ColorManager(),
        private EncodingManager $encodingManager = new EncodingManager(),
        private OutputManager $OutputManager = new OutputManager()
    ) {
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
     * Generates a QR code based on the specified output format
     *
     * @param string $format The desired format for the Normal QR code('gif', 'png', 'svg', 'webp', 'eps', 'pdf', 'binary')
     * @param string $format The desired format for the Custom QR code('png-M(1-3)-C(1-3)')
     * @return self The current QRCodeGenerator instance for method chaining
     * @throws InvalidArgumentException If the specified format is not supported
     */
    public function generate(string $format = 'png'): self
    {
        $this->builder = (new Builder(
            writer: $this->validateWriter($format),
            writerOptions: [],
            validateResult: false,
            data: $this->data,
            encoding: $this->encodingManager->getEncoding(),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $this->size,
            margin: $this->margin,
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
     * @throws RuntimeException If the QR code has not been generated yet
     */
    public function getMatrix(): Matrix
    {
        $this->ensureBuilderExists();

        return $this->OutputManager->getMatrix($this->builder);
    }

    /**
     * Get the matrix as an array
     *
     * @return array The QR code matrix represented as a 2D array
     * @throws RuntimeException If no QR code has been generated yet
     */
    public function getMatrixAsArray(): array
    {
        $this->ensureBuilderExists();

        return $this->OutputManager->getMatrixAsArray($this->builder);
    }

    /**
     * Returns the QR code as a raw string
     *
     * @return string The raw string representation of the QR code
     * @throws RuntimeException If the QR code has not been generated yet
     */
    public function getString(): string
    {
        $this->ensureBuilderExists();

        return $this->OutputManager->getString($this->builder);
    }

    /**
     * Returns the QR code as a Base64-encoded data URI
     *
     * @return string The data URI representation of the QR code
     * @throws RuntimeException If the QR code has not been generated yet
     */
    public function getDataUri(): string
    {
        $this->ensureBuilderExists();

        return $this->OutputManager->getDataUri($this->builder);
    }

    /**
     * Save the generated QR code to a file
     * 
     * @param string $path The path to save the QR code file
     * @return bool True if the file was saved successfully, false otherwise
     * @throws InvalidArgumentException If the format is unsupported
     * @throws RuntimeException If no QR code has been generated yet
     */
    public function saveTo(string $path): bool
    {
        $this->ensureBuilderExists();

        return $this->OutputManager->saveTo($this->builder, $path);
    }

    /**
     * Set the data to be encoded in the QR code
     *
     * @param string $data The data to encode
     * @return self
     * @throws InvalidArgumentException If the data is empty
     */
    public function setData(string $data, DataType $type = DataType::Text): self
    {
        $class = $type->value;

        if (!$class::validate($data)) {
            throw new \InvalidArgumentException("Invalid data for type: " . $class::getType());
        }

        if (empty(trim($data))) {
            throw new InvalidArgumentException('Data cannot be empty.');
        }

        $this->data = $this->dataSanitizer($data, $type);
        return $this;
    }

    /**
     * Retrieves the data value
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
     * @throws InvalidArgumentException If the size is not a positive integer
     */
    public function setSize(int $size): self
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Size must be a positive integer.');
        }

        $this->size = $size;
        return $this;
    }

    /**
     * Retrieves the size value
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
     * @throws InvalidArgumentException If the margin is negative
     */
    public function setMargin(int $margin): self
    {
        if ($margin < 0) {
            throw new InvalidArgumentException('Margin cannot be negative.');
        }

        $this->margin = $margin;
        return $this;
    }

    /**
     * Retrieves the margin value
     *
     * @return int The margin value
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * Set the color of the QR code foreground
     *
     * @param string $hexColor The hexadecimal color code
     * @return self
     * @throws InvalidArgumentException If the color format is invalid
     */
    public function setColor(string $hexColor): self
    {
        if (!$this->isValidHexColor($hexColor)) {
            throw new InvalidArgumentException('Invalid color format');
        }

        $this->colorManager->setColor($hexColor);
        return $this;
    }

    /**
     * Retrieves the color from the color manager
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
     * @throws InvalidArgumentException If the color format is invalid
     */
    public function setBackgroundColor(string $hexColor): self
    {
        if (!$this->isValidHexColor($hexColor)) {
            throw new InvalidArgumentException('Invalid background color format');
        }

        $this->colorManager->setBackgroundColor($hexColor);
        return $this;
    }

    /**
     * Retrieves the background color from the color manager
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
     * @param int $logoSize The size of the logo
     * @return self
     * @throws InvalidArgumentException If the logo file does not exist
     */
    public function setLogo(string $logoPath, int $logoSize = 40): self
    {
        if (!file_exists($logoPath)) {
            throw new InvalidArgumentException('Logo File Does Not Exist');
        }

        $this->logoManager->setLogo($logoPath);
        $this->logoManager->setLogoSize($logoSize);
        return $this;
    }

    /**
     * Retrieves the logo path from the logo manager
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
     * @param int $fontSize The font size of the label text (default is 20)
     * @param array $margin The margin around the label in the format [top, right, bottom, left] (default is [0, 10, 10, 10])
     * @return self Returns the current instance for method chaining
     * @throws InvalidArgumentException If the label text is empty
     */
    public function setLabel(
        string $label,
        string $textAlign = 'center',
        string $textColor = '#000000',
        int $fontSize = 20,
        array $margin = [0, 10, 10, 10]
    ): self {
        if (empty($label)) {
            throw new InvalidArgumentException('Label cannot be empty');
        }

        $this->labelManager->setLabel($label);
        $this->labelManager->setLabelAlign($textAlign);
        $this->labelManager->setLabelColor($textColor);
        $this->labelManager->setLabelSize($fontSize);
        $this->labelManager->setLabelMargin($margin);

        return $this;
    }

    /**
     * Retrieves the label from the label manager
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
     * @param DataType $type The type of data being encoded (Url, Wifi, Location, Text, Email, Phone)
     * @return string Sanitized and properly formatted data string
     */
    private function dataSanitizer(string $data, DataType $type)
    {
        $data = htmlspecialchars($data);

        $extension = match ($type) {
            DataType::Email => "mailto:{$data}",
            DataType::Phone => "tel:{$data}",
            DataType::Wifi => "$data",
            DataType::Location => "https://www.google.com/maps?q=$data",
            default => $data,
        };

        return $extension;
    }

    /**
     * Helper method to validate the writer for the given format
     *
     * @param string $format The format to validate
     * @return WriterInterface The writer interface instance for the specified format
     * @throws InvalidArgumentException If the format is invalid
     */
    private function validateWriter(string $format): WriterInterface
    {
        if (preg_match('/^(\w+)-(M\d{1,2})-(C\d{1,2})$/i', $format, $matches)) {

            $customWriterClass = 'HeroQR\Core\Writers\Custom' . ucfirst($matches[1]) . 'Writer';

            if (!class_exists($customWriterClass)) {
                throw new InvalidArgumentException(sprintf('Custom Format "%s" Does Not Exist', $format));
            }

            return new $customWriterClass($matches[2], $matches[3]);
        }

        if ($format === 'pdf' && !class_exists('FPDF')) {
            throw new RuntimeException('The library "<a href="https://github.com/Setasign/FPDF" target="_blank" style="text-decoration: none;">setasign/fpdf</a>" is required for PDF generation. Please install it using "composer require setasign/fpdf".');
        }

        $writerClass = 'Endroid\QrCode\Writer\\' . ucfirst($format) . 'Writer';

        if (!class_exists($writerClass)) {
            throw new InvalidArgumentException(sprintf('Format "%s" Does Not Exist', $format));
        }

        $writer = new $writerClass();

        if (!$writer instanceof WriterInterface) {
            throw new InvalidArgumentException(sprintf('Format "%s" Is Not Supported', $format));
        }

        return $writer;
    }

    /**
     * Helper method to validate if a hex color is valid
     *
     * @param string $hexColor The color code to validate
     * @return bool True if the color code is valid, false otherwise
     */
    private function isValidHexColor(string $hexColor): bool
    {
        return preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6}|[a-fA-F0-9]{8})$/', $hexColor) === 1;
    }

    /**
     * Helper method to ensure that the builder has been initialized
     *
     * @throws RuntimeException If the builder has not been initialized
     */
    private function ensureBuilderExists(): void
    {
        if (!$this->builder) {
            throw new RuntimeException('No QR Code has been generated. Call generate() first.');
        }
    }
}