<?php

declare(strict_types=1);

namespace HeroQR\Managers;

use HeroQR\Contracts\Managers\LogoManagerInterface;

/**
 * Manages logo-related settings for QR code integration, including file path, size, and background options
 * Provides methods to set and retrieve logo properties for customization
 * 
 * @package HeroQR\Managers
 */

class LogoManager implements LogoManagerInterface
{
    private string $logoPath = '';
    private int $logoSize = 80;
    private bool $logoBackground = false;

    /**
     * Set the logo path
     * 
     * @param string $logoPath The file path to the logo
     * @throws \InvalidArgumentException If the file does not exist or is not readable
     */
    public function setLogo(string $logoPath): void
    {
        if (!file_exists($logoPath) || !is_readable($logoPath)) {
            throw new \InvalidArgumentException("Logo Path '{$logoPath}' Does Not Exist Or Is Not Readable");
        }

        $this->logoPath = $logoPath;
    }

    /**
     * Get the current logo path
     * 
     * @return string The logo file path
     */
    public function getLogoPath(): string
    {
        return $this->logoPath;
    }

    /**
     * Set whether the logo should have a background
     * 
     * @param bool $logoBackground True if the logo should have a background, false otherwise
     */
    public function setLogoBackground(bool $logoBackground): void
    {
        $this->logoBackground = $logoBackground;
    }

    /**
     * Get the current logo background setting
     * 
     * @return bool True if the logo has a background, false otherwise
     */
    public function getLogoBackground(): bool
    {
        return $this->logoBackground;
    }

    /**
     * Set the logo size
     * 
     * @param int $size The size of the logo
     * @throws \InvalidArgumentException If the size is not a positive integer
     */
    public function setLogoSize(int $size): void
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Logo Size Must Be A Positive Integer');
        }

        $this->logoSize = $size;
    }

    /**
     * Get the current logo size
     * 
     * @return int The size of the logo
     */
    public function getLogoSize(): int
    {
        return $this->logoSize;
    }
}
