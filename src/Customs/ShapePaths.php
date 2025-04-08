<?php

namespace HeroQR\Customs;

use HeroQR\Contracts\Customs\AbstractCustomPaths;

/**
 * A class that manages paths for marker images, providing methods to retrieve paths,
 * validate keys, and get the specific path for a marker based on its key
 *
 * @package HeroQR\Customs
 */
class ShapePaths extends AbstractCustomPaths
{
    public const S1 = 'drawSquare';
    public const S2 = 'drawCircle';
    public const S3 = 'drawStar';
    public const S4 = 'drawDiamond';

    /**
     * Retrieves all marker paths as an associative array
     */
    public static function getAllPaths(): array
    {
        $reflection = new \ReflectionClass(static::class);
        $constants = $reflection->getConstants();

        return array_filter($constants, fn($key) => str_starts_with($key, 'S'), ARRAY_FILTER_USE_KEY);
    }
}
