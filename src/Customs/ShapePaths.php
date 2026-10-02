<?php

namespace HeroQR\Customs;

use HeroQR\Contracts\Customs\AbstractCustomPaths;

/**
 * A class that manages paths for marker images, providing methods to retrieve paths,
 * validate keys, and get the specific path for a marker based on its key
 */
class ShapePaths extends AbstractCustomPaths
{
    private const SHAPES = [
        'S1' => 'drawSquare',
        'S2' => 'drawCircle',
        'S3' => 'drawStar',
        'S4' => 'drawDiamond',
    ];

    /**
     * Retrieves all marker paths as an associative array
     *
     * @return array<string, string>
     */
    public static function getAllPaths(): array
    {
        return self::SHAPES;
    }
}
