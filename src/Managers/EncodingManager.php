<?php

namespace HeroQR\Managers;

use HeroQR\Contracts\Managers\EncodingManagerInterface;
use Endroid\QrCode\Encoding\EncodingInterface;
use Endroid\QrCode\Encoding\Encoding;
use InvalidArgumentException;

/**
 * Class EncodingManager
 * Manages the character encoding for QR codes.
 */
class EncodingManager implements EncodingManagerInterface
{
    private EncodingInterface $encoding;

    /**
     * EncodingManager constructor.
     * Initializes the encoding with a default value of 'UTF-8'.
     */
    public function __construct()
    {
        $this->encoding = new Encoding('UTF-8');
    }

    /**
     * Get the current encoding.
     * 
     * @return EncodingInterface The current encoding setting.
     */
    public function getEncoding(): EncodingInterface
    {
        return $this->encoding;
    }

    /**
     * Set a new encoding.
     * 
     * @param string $encoding The desired encoding ('UTF-16' ,'UTF-8', 'ASCII', 'ISO-8859-1', 'ISO-8859-5', 'ISO-8859-15') and more...
     * @throws InvalidArgumentException If the encoding is invalid or unsupported.
     */
    public function setEncoding(string $encoding): void
    {
        if (empty($encoding)) {
            throw new InvalidArgumentException('Encoding Cannot Be Empty');
        }

        $this->encoding = new Encoding($encoding);
    }
}
