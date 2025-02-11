<?php

namespace HeroQR\Contracts\Managers;

use RuntimeException;
use InvalidArgumentException;
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
    abstract protected function getCustomWriter(string $format, array $customs): WriterInterface;
    abstract protected function getStandardWriter(string $format): WriterInterface;
    abstract public function getWriter(string $format, array $customs = []): WriterInterface;

    /**
     * Checks if a class exists and implements WriterInterface
     *
     * @param string $writerClass The fully qualified class name
     * @return void
     * @throws InvalidArgumentException
     */
    protected function validateWriterClass(string $writerClass): void
    {
        if (!class_exists($writerClass) || !is_subclass_of($writerClass, WriterInterface::class)) {
            throw new InvalidArgumentException(sprintf('Invalid writer class: %s', $writerClass));
        }
    }

    /**
     * Ensures that the required library is installed for a specific functionality
     *
     * @param string $class            The class name to check for existence
     * @param string $composerPackage  The Composer package name
     * @param string $url              The URL to the library documentation
     * @throws RuntimeException
     */
    protected function ensureLibraryInstalled(string $class, string $composerPackage, string $url): void
    {
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf(
                'The library "<a href="%s" target="_blank" style="text-decoration: none;">%s</a>" is required. Please install it using "composer require %s".',
                $url,
                $composerPackage,
                $composerPackage
            ));
        }
    }

    /**
     * Checks if the custom array has at least one valid value
     *
     * @param array $customs  The array of custom values
     * @return bool
     */
    protected function hasValidCustoms(array $customs): bool
    {
        $patterns = [
            '/^M\d{1,2}$/i',
            '/^C\d{1,2}$/i',
            '/^S\d{1,2}$/i',
        ];

        foreach ($customs as $value) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Finds a custom value in the given array based on a regex pattern
     * Returns the default value if no match is found
     *
     * @param array $customs    The array of custom values
     * @param string $pattern   The regex pattern to match
     * @param string $default   The default value to return if no match is found
     * @return string
     */
    protected function findCustomValue(array $customs, string $pattern, string $default): string
    {
        foreach ($customs as $value) {
            if (preg_match($pattern, $value)) {
                return $value;
            }
        }
        return $default;
    }
}
