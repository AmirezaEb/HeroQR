<?php

declare(strict_types=1);

namespace HeroQR\Managers;

use Endroid\QrCode\Writer\WriterInterface;
use HeroQR\Contracts\Managers\AbstractWriterManager;

/**
 * Manages and creates QR code writer instances
 * 
 * This class handles the creation and validation of both standard and custom QR code writers.
 * It ensures proper initialization of writers based on the requested format and custom parameters.
 * 
 * @package HeroQR\Managers
 */
class WriterManager extends AbstractWriterManager
{
    /**
     * Maps standard format names to their respective writer classes
     */
    protected const STANDARD_WRITERS = [
        'png' => 'Endroid\\QrCode\\Writer\\PngWriter',
        'svg' => 'Endroid\\QrCode\\Writer\\SvgWriter',
        'eps' => 'Endroid\\QrCode\\Writer\\EpsWriter',
        'pdf' => 'Endroid\\QrCode\\Writer\\PdfWriter',
        'binary' => 'Endroid\\QrCode\\Writer\\BinaryWriter',
        'webp' => 'Endroid\\QrCode\\Writer\\WebpWriter',
        'gif' => 'Endroid\\QrCode\\Writer\\GifWriter'
    ];

    /**
     * Maps formats to their custom writer implementations
     */
    protected const CUSTOM_WRITERS = [
        'png' => 'HeroQR\Core\Writers\CustomPngWriter'
    ];

    /**
     * Defines valid prefixes for custom parameters
     */
    protected const CUSTOM_PREFIXES = [
        'Marker' => 'M',
        'Cursor' => 'C',
        'Shape' => 'S'
    ];

    /**
     * Returns a writer based on format and custom settings.
     *
     * @param string $format The desired output format.
     * @param array $customs (Optional) Custom settings for the writer.
     * @return WriterInterface A writer for the specified format and settings.
     */
    public function getWriter(string $format, array $customs = []): WriterInterface
    {
        $format = strtolower(trim($format));

        if (!isset(self::STANDARD_WRITERS[$format])) {
            throw new \InvalidArgumentException(
                "Unsupported format '{$format}'. Supported formats: " .
                    implode(', ', array_keys(self::STANDARD_WRITERS))
            );
        }

        return match (true) {
            $this->hasCustomParameters($customs) => $this->createCustomWriter($format, $customs),
            default => $this->createStandardWriter($format)
        };
    }

    /**
     * Returns a custom writer based on format and custom settings.
     *
     * @param string $format The desired output format.
     * @param array $customs Custom settings for the writer.
     * @return WriterInterface A custom writer for the specified format and settings.
     */
    protected function getCustomWriter(string $format, array $customs): WriterInterface
    {
        return $this->createCustomWriter($format, $customs);
    }

    /**
     * Returns a standard writer based on the format.
     *
     * @param string $format The desired output format.
     * @return WriterInterface A standard writer for the specified format.
     */
    protected function getStandardWriter(string $format): WriterInterface
    {
        return $this->createStandardWriter($format);
    }

    /**
     * Creates a custom writer instance with specified parameters
     * 
     * @param string $format The desired output format
     * @param array $customs Customization parameters
     * @return WriterInterface
     * @throws \InvalidArgumentException If custom writer is not supported for format
     * @throws \RuntimeException If writer class cannot be instantiated
     */
    protected function createCustomWriter(string $format, array $customs): WriterInterface
    {
        if (!isset(self::CUSTOM_WRITERS[$format])) {
            throw new \InvalidArgumentException("Custom writers not supported for '{$format}'");
        }

        $writerClass = self::CUSTOM_WRITERS[$format];

        if (!class_exists($writerClass)) {
            throw new \RuntimeException("Writer class '{$writerClass}' not found");
        }

        $parameters = array_map(
            fn(string $key, string $prefix) => $this->validatePattern($customs[$key] ?? "{$prefix}1", $prefix),
            array_keys(self::CUSTOM_PREFIXES),
            self::CUSTOM_PREFIXES
        );

        return new $writerClass(...$parameters);
    }

    /**
     * Creates a standard writer instance
     * 
     * @param string $format The desired output format
     * @return WriterInterface
     * @throws \RuntimeException If writer class cannot be instantiated
     */
    protected function createStandardWriter(string $format): WriterInterface
    {
        $writerClass = self::STANDARD_WRITERS[$format];

        if (!class_exists($writerClass)) {
            throw new \RuntimeException("Writer class '{$writerClass}' not found");
        }

        return new $writerClass();
    }

    /**
     * Checks if the customs array contains any valid custom parameters
     * 
     * @param array $customs The customs array to check
     * @return bool True if valid custom parameters exist
     */
    protected function hasCustomParameters(array $customs): bool
    {
        return (bool) array_intersect_key($customs, self::CUSTOM_PREFIXES);
    }

    /**
     * Validates and normalizes a custom pattern string
     * 
     * @param string $value The pattern value to validate
     * @param string $prefix The expected prefix (M, C, or S)
     * @return string The validated and normalized pattern
     * @throws \InvalidArgumentException If pattern is invalid
     */
    protected function validatePattern(string $value, string $prefix): string
    {
        $value = strtoupper(trim($value));
        if (!preg_match("/^{$prefix}\d{1,2}$/", $value)) {
            throw new \InvalidArgumentException("Invalid pattern '{$value}' for prefix '{$prefix}'");
        }
        return $value;
    }
}
