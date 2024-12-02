<?php

namespace HeroQR\Contracts\services;

use Endroid\QrCode\Label\Margin\MarginInterface;
use Endroid\QrCode\Label\Font\FontInterface;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Label\LabelAlignment;

interface LabelManagerInterface
{
    /**
     * Set the label text.
     * 
     * @param string $label The text to display on the label.
     */
    public function setLabel(string $label): void;

    /**
     * Get the current label text.
     * 
     * @return string
     */
    public function getLabel(): string;

    /**
     * Set the font size for the label.
     * 
     * @param int $size The font size to apply.
     */
    public function setLabelSize(int $size): void;

    /**
     * Get the font used for the label.
     * 
     * @return FontInterface
     */
    public function getLabelFont(): FontInterface;

    /**
     * Set the alignment for the label.
     * 
     * @param string $labelAlign The alignment (left, center, or right).
     */
    public function setLabelAlign(string $labelAlign): void;

    /**
     * Get the current label alignment.
     * 
     * @return LabelAlignment
     */
    public function getLabelAlign(): LabelAlignment;

    /**
     * Set the label color.
     * 
     * @param string $color The color in hex format (e.g., "#FF5733").
     */
    public function setLabelColor(string $color): void;

    /**
     * Get the current label color.
     * 
     * @return ColorInterface
     */
    public function getLabelColor(): ColorInterface;

    /**
     * Set the label margin.
     * 
     * @param array $margin An array of margin values [top, right, bottom, left].
     */
    public function setLabelMargin(array $margin): void;

    /**
     * Get the current label margin.
     * 
     * @return MarginInterface
     */
    public function getLabelMargin(): MarginInterface;
}
