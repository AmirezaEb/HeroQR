<?php

namespace HeroQR\Contracts\services;

interface LogoManagerInterface
{
    /**
     * Set the logo path.
     * 
     * @param string $logoPath The file path to the logo.
     */
    public function setLogo(string $logoPath): void;

    /**
     * Get the current logo path.
     * 
     * @return string The logo file path.
     */
    public function getLogoPath(): string;

    /**
     * Set whether the logo should have a background.
     * 
     * @param bool $logoBackground True if the logo should have a background, false otherwise.
     */
    public function setLogoBackground(bool $logoBackground): void;

    /**
     * Get the current logo background setting.
     * 
     * @return bool True if the logo has a background, false otherwise.
     */
    public function getLogoBackground(): bool;

    /**
     * Set the logo size.
     * 
     * @param int $size The size of the logo.
     */
    public function setLogoSize(int $size): void;

    /**
     * Get the current logo size.
     * 
     * @return int The size of the logo.
     */
    public function getLogoSize(): int;
}
