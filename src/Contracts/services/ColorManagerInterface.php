<?php

namespace HeroQR\Contracts\services;

use Endroid\QrCode\Color\ColorInterface;

interface ColorManagerInterface
{
    /**
     * Set the main color.
     *
     * @param string $hexColor Hexadecimal color code.
     * @return void
     */
    public function setColor(string $hexColor): void;

    /**
     * Get the main color.
     *
     * @return ColorInterface
     */
    public function getColor(): ColorInterface;

    /**
     * Set the background color.
     *
     * @param string $hexColor Hexadecimal color code.
     * @return void
     */
    public function setBackgroundColor(string $hexColor): void;

    /**
     * Get the background color.
     *
     * @return ColorInterface
     */
    public function getBackgroundColor(): ColorInterface;

    /**
     * Set the label color.
     *
     * @param string $hexColor Hexadecimal color code.
     * @return void
     */
    public function setLabelColor(string $hexColor): void;

    /**
     * Get the label color.
     *
     * @return ColorInterface
     */
    public function getLabelColor(): ColorInterface;
}
