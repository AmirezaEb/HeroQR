<?php

namespace HeroQR\Contracts\Managers;

/**
 * Interface LogoManagerInterface
 *
 * This interface defines methods for managing the logo that can be included
 * in a QR code. It provides functionality for setting the logo's file path,
 * size, background visibility, and retrieving those properties.
 *
 * @package HeroQR\Contracts\Managers
 */
interface LogoManagerInterface
{
    /**
     * Set the logo file path
     *
     * This method allows you to specify the file path of the logo that will
     * be placed on the QR code. The logo can be any image file (e.g., PNG, JPEG)
     *
     * @param string $logoPath The file path to the logo image
     * @return void
     */
    public function setLogo(string $logoPath): void;

    /**
     * Get the current logo file path
     *
     * This method retrieves the current file path of the logo that is set for
     * the QR code. It will return the file path of the logo
     *
     * @return string The file path to the logo image
     */
    public function getLogoPath(): string;

    /**
     * Set whether the logo should have a background
     *
     * This method allows you to set if the logo should be displayed with a background
     * This can help improve the visibility of the logo, especially if the QR code
     * contains colors that make the logo less visible.
     *
     * @param bool $logoBackground True if the logo should have a background, false otherwise.
     * @return void
     */
    public function setLogoBackground(bool $logoBackground): void;

    /**
     * Get the current logo background setting
     *
     * This method retrieves the current setting for whether the logo should have a background
     * It returns `true` if the logo has a background and `false` otherwise
     *
     * @return bool True if the logo has a background, false otherwise
     */
    public function getLogoBackground(): bool;

    /**
     * Set the logo size
     *
     * This method defines the size of the logo relative to the QR code
     * The size can be set in pixels or as a percentage of the QR code's overall size
     *
     * @param int $size The size of the logo in pixels or percentage
     * @return void
     */
    public function setLogoSize(int $size): void;

    /**
     * Get the current logo size
     *
     * This method retrieves the current size of the logo. The size is returned
     * in pixels or as a percentage of the QR code's size, depending on how it was set
     *
     * @return int The size of the logo in pixels or percentage
     */
    public function getLogoSize(): int;
}
