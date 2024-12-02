<?php

namespace HeroQR\Services;

use HeroQR\Contracts\services\LogoManagerInterface;
use InvalidArgumentException;

/**
 * Class LogoManager
 * Manages logo settings such as path, size, and background visibility for QR codes.
 */
class LogoManager implements LogoManagerInterface
{
    private string $logoPath = '';
    private int $logoSize = 40;
    private bool $logoBackground = false;

    /**
     * Set the logo path.
     * 
     * @param string $logoPath The file path to the logo.
     * @throws InvalidArgumentException If the file does not exist or is not readable.
     */
    public function setLogo(string $logoPath): void
    {
        if (!file_exists($logoPath) || !is_readable($logoPath)) {
            throw new InvalidArgumentException("Logo path '{$logoPath}' does not exist or is not readable.");
        }

        $this->logoPath = $logoPath;
    }

    /**
     * Get the current logo path.
     * 
     * @return string The logo file path.
     */
    public function getLogoPath(): string
    {
        return $this->logoPath;
    }

    /**
     * Set whether the logo should have a background.
     * 
     * @param bool $logoBackground True if the logo should have a background, false otherwise.
     */
    public function setLogoBackground(bool $logoBackground): void
    {
        $this->logoBackground = $logoBackground;
    }

    /**
     * Get the current logo background setting.
     * 
     * @return bool True if the logo has a background, false otherwise.
     */
    public function getLogoBackground(): bool
    {
        return $this->logoBackground;
    }

    /**
     * Set the logo size.
     * 
     * @param int $size The size of the logo.
     * @throws InvalidArgumentException If the size is not a positive integer.
     */
    public function setLogoSize(int $size): void
    {
        if ($size <= 0) {
            throw new InvalidArgumentException('Logo size must be a positive integer.');
        }

        $this->logoSize = $size;
    }

    /**
     * Get the current logo size.
     * 
     * @return int The size of the logo.
     */
    public function getLogoSize(): int
    {
        return $this->logoSize;
    }
}
