<?php

namespace HeroQR\Contracts\Managers;

use Endroid\QrCode\Writer\WriterInterface;

/**
 * Abstract Class AbstractWriterManager
 *
 * Provides a foundational structure for managing QR code writers within the HeroQR library.
 * This class enforces consistent implementation of writer management logic for different formats
 * and offers utility methods for validation and customization handling.
 *
 * @package HeroQR\Contracts\Managers
 */
abstract class AbstractWriterManager
{
    /**
     * Returns a custom writer based on format and custom settings.
     *
     * @param string $format The desired output format.
     * @param array $customs Custom settings for the writer.
     * @return WriterInterface A custom writer for the specified format and settings.
     */
    abstract protected function getCustomWriter(string $format, array $customs): WriterInterface;

    /**
     * Returns a standard writer based on the format.
     *
     * @param string $format The desired output format.
     * @return WriterInterface A standard writer for the specified format.
     */
    abstract protected function getStandardWriter(string $format): WriterInterface;

    /**
     * Returns a writer based on format and custom settings.
     *
     * @param string $format The desired output format.
     * @param array $customs (Optional) Custom settings for the writer.
     * @return WriterInterface A writer for the specified format and settings.
     */
    abstract public function getWriter(string $format, array $customs = []): WriterInterface;
}
