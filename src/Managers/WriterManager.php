<?php

namespace HeroQR\Managers;

use InvalidArgumentException;
use Endroid\QrCode\Writer\WriterInterface;
use HeroQR\Contracts\Managers\AbstractWriterManager;

/**
 * Responsible for managing the creation and retrieval of QR code writers
 * Supports both standard and custom writers for various QR code formats,
 * enabling flexibility and customization
 * 
 * @package HeroQR\Managers
 */

class WriterManager extends AbstractWriterManager
{
    /**
     * Retrieves a writer instance based on the provided format and custom values
     *
     * @param string $format  The format of the writer ("svg", "png", "pdf")
     * @param array $customs  Optional custom parameters
     * @return WriterInterface
     */
    public function getWriter(string $format, array $customs = []): WriterInterface
    {
        if ($this->hasCustomParameters($customs)) {
            if ($format !== 'png') {
                throw new InvalidArgumentException(sprintf(
                    'Customization is not supported for the "%s" format. Please use "png" for customizations.',
                    $format
                ));
            }
            return $this->getCustomWriter($format, $customs);
        }

        if ($format === 'pdf') {
            $this->ensureLibraryInstalled('FPDF', 'setasign/fpdf', 'https://github.com/Setasign/FPDF');
        }

        return $this->getStandardWriter($format);
    }

    /**
     * Retrieves the custom writer instance based on the provided format and custom values
     *
     * @param string $format  The format string ("png")
     * @param array $customs  An array of custom parameters with keys like 'marker', 'cursor', and 'shape'
     * @return WriterInterface
     * @throws InvalidArgumentException
     */
    protected function getCustomWriter(string $format, array $customs): WriterInterface
    {
        $customWriterClass = 'HeroQR\Core\Writers\Custom' . ucfirst($format) . 'Writer';
        if (!class_exists($customWriterClass)) {
            throw new InvalidArgumentException(sprintf(
                'Custom Writer for format "%s" does not exist. Ensure the writer class "%s" is implemented.',
                $format,
                $customWriterClass
            ));
        }

        $marker = $customs['Marker'] ?? 'M1';
        $cursor = $customs['Cursor'] ?? 'C1';
        $shape  = $customs['Shape'] ?? 'S1';

        return new $customWriterClass($marker, $cursor, $shape);
    }

    /**
     * Retrieves the standard writer instance based on the provided format
     *
     * @param string $format  The standard format string ("webp", "png", "svg" ,and more...)
     * @return WriterInterface
     * @throws InvalidArgumentException
     */
    protected function getStandardWriter(string $format): WriterInterface
    {
        $writerClass = 'Endroid\QrCode\Writer\\' . ucfirst($format) . 'Writer';
        if (!class_exists($writerClass)) {
            throw new InvalidArgumentException(sprintf(
                'Format "%s" is not supported. Supported formats are: png, svg, pdf, webp, gif, binary, eps.',
                $format
            ));
        }

        $writer = new $writerClass();
        if (!$writer instanceof WriterInterface) {
            throw new InvalidArgumentException(sprintf('Format "%s" Is Not Supported', $format));
        }

        return $writer;
    }

    /**
     * Checks if custom parameters are provided
     *
     * @param array $customs  The array of custom parameters
     * @return bool
     */
    protected function hasCustomParameters(array $customs): bool
    {
        return !empty($customs['Marker']) || !empty($customs['Cursor']) || !empty($customs['Shape']);
    }
}
