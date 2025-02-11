<?php

namespace HeroQR\Managers;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use HeroQR\Contracts\Managers\ColorManagerInterface;

/**
 * Manages the foreground, background, and label colors for QR codes
 * Allows setting and getting colors in hexadecimal format and converts them to RGB or RGBA values
 * The class ensures proper handling of colors for the QR code, including support for alpha transparency
 *
 * @package HeroQR\Managers
 */

class ColorManager implements ColorManagerInterface
{
    /**
     * ColorManager constructor
     * 
     * Initializes default colors for the QR code: color (black), background (white), and label (black)
     * 
     * @param ColorInterface $color Default color of the QR code (black)
     * @param ColorInterface $backgroundColor Default background color of the QR code (white)
     * @param ColorInterface $labelColor Default label color (black)
     */
    public function __construct(
        private ColorInterface $color = new Color(0, 0, 0, 0), # Default black
        private ColorInterface $backgroundColor = new Color(255, 255, 255, 0), # Default white
        private ColorInterface $labelColor = new Color(0, 0, 0, 0), # Default black
    ) {}

    /**
     * Set the foreground color of the QR code
     * 
     * Converts the provided hex color string to an RGB value and assigns it to the QR code's color property
     *
     * @param string $hexColor The hex color string ( #ff0000,'#ffffffFF')
     */
    public function setColor(string $hexColor): void
    {
        $this->color = $this->hex2rgb($hexColor);
    }

    /**
     * Get the foreground color of the QR code
     * 
     * @return ColorInterface The current color of the QR code
     */
    public function getColor(): ColorInterface
    {
        return $this->color;
    }

    /**
     * Set the background color of the QR code
     * 
     * Converts the provided hex color string to an RGB value and assigns it to the QR code's background color property
     *
     * @param string $hexColor The hex color string ( #ffffff,#ffffffFF)
     */
    public function setBackgroundColor(string $hexColor): void
    {
        $this->backgroundColor = $this->hex2rgb($hexColor);
    }

    /**
     * Get the background color of the QR code
     * 
     * @return ColorInterface The current background color of the QR code
     */
    public function getBackgroundColor(): ColorInterface
    {
        return $this->backgroundColor;
    }

    /**
     * Set the label color of the QR code
     * 
     * Converts the provided hex color string to an RGB value and assigns it to the QR code's label color property
     *
     * @param string $hexColor The hex color string ( #000000)
     */
    public function setLabelColor(string $hexColor): void
    {
        $this->labelColor = $this->hex2rgb($hexColor);
    }

    /**
     * Get the label color of the QR code
     * 
     * @return ColorInterface The current label color of the QR code
     */
    public function getLabelColor(): ColorInterface
    {
        return $this->labelColor;
    }

    /**
     * Convert a hex color string to RGB format
     * 
     * If the hex color includes an alpha component, it converts it to an appropriate value between 0 and 127 for use with GD functions
     * 
     * @param string $hexColor The hex color string, optionally with an alpha channel (#000000, #ff0000ff)
     * 
     * @return ColorInterface The corresponding Color object with RGB and alpha values
     */
    private function hex2rgb(string $hexColor): ColorInterface
    {
        $hexColor = ltrim($hexColor, '#');

        if (strlen($hexColor) === 8) {

            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $a = hexdec(substr($hexColor, 6, 2));

            return new Color($r, $g, $b, (int)($a / 2));
        }

        elseif (strlen($hexColor) === 6) {

            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));

            return new Color($r, $g, $b); 
        }

        return new Color(0, 0, 0);
    }
}
