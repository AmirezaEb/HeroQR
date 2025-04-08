<?php

namespace HeroQR\Contracts\Managers;

use Endroid\QrCode\Encoding\EncodingInterface;

/**
 * Interface ColorManagerInterface
 *
 * Defines the contract for managing QR code colors, including the main color,
 * background color, and label color. Each method allows setting and retrieving
 * color values, ensuring consistency and flexibility in QR code customization.
 *
 * @package HeroQR\Contracts\Managers
 */
interface EncodingManagerInterface
{
    /**
     * Get the current encoding
     *
     * @return EncodingInterface
     */
    public function getEncoding(): EncodingInterface;

    /**
     * Set a new encoding
     *
     * @param string $encoding The desired encoding (e.g., 'UTF-8', 'ISO-8859-1')
     * @return void
     */
    public function setEncoding(string $encoding): void;
}
