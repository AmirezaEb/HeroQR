<?php

namespace HeroQR\Managers;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use HeroQR\Contracts\Managers\ColorManagerInterface;

class ColorManager implements ColorManagerInterface
{
    public function __construct(
        private ColorInterface $color = new Color(0, 0, 0), // Default black
        private ColorInterface $backgroundColor = new Color(255, 255, 255), // Default white
        private ColorInterface $labelColor = new Color(0, 0, 0), // Default white
    ) {}

    public function setColor(string $hexColor): void
    {
        $this->color = $this->hex2rgb($hexColor);
    }

    public function getColor(): ColorInterface
    {
        return $this->color;
    }

    public function setBackgroundColor(string $hexColor): void
    {
        $this->backgroundColor = $this->hex2rgb($hexColor);
    }

    public function getBackgroundColor(): ColorInterface
    {
        return $this->backgroundColor;
    }

    public function setLabelColor(string $hexColor): void
    {
        $this->labelColor = $this->hex2rgb($hexColor);
    }

    public function getLabelColor(): ColorInterface
    {
        return $this->labelColor;
    }

    private function hex2rgb(string $hexColor): ColorInterface
    {
        $hexColor = ltrim($hexColor, '#');
        if (strlen($hexColor) == 6) {
            list($r, $g, $b) = str_split($hexColor, 2);
            return new Color(hexdec($r), hexdec($g), hexdec($b));
        }
        return new Color(0, 0, 0); // Default black if invalid hex
    }
}
