<?php

namespace HeroQR\Contracts\services;

use Endroid\QrCode\Encoding\EncodingInterface;

interface EncodingManagerInterface
{
    /**
     * Get the current encoding.
     * 
     * @return EncodingInterface
     */
    public function getEncoding(): EncodingInterface;

    /**
     * Set a new encoding.
     * 
     * @param string $encoding The desired encoding (e.g., 'UTF-8', 'ISO-8859-1').
     * @return void
     */
    public function setEncoding(string $encoding): void;
}
