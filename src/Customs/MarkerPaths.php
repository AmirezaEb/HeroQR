<?php

namespace HeroQR\Customs;

use HeroQR\Contracts\Customs\AbstractCustomPaths;

class MarkerPaths extends AbstractCustomPaths
{
    private const PATH = __DIR__ . '/../../assets/Markers/';

    /**
     *  Retrieves all marker paths as an associative array
     *
     * @return array<string, string>
     */
    public static function getAllPaths(): array
    {
        return [
            'M1' => self::PATH . 'Marker-1.png',
            'M2' => self::PATH . 'Marker-2.png',
            'M3' => self::PATH . 'Marker-3.png',
            'M4' => self::PATH . 'Marker-4.png',
            'M5' => self::PATH . 'Marker-5.png',
            'M6' => self::PATH . 'Marker-6.png',
            'M7' => self::PATH . 'Marker-7.png',
        ];
    }
}