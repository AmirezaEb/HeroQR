<?php

namespace HeroQR\Customs;

use HeroQR\Contracts\Customs\AbstractCustomPaths;

/**
 * A class that manages paths for marker images, providing methods to retrieve paths,
 * validate keys, and get the specific path for a marker based on its key
 *
 * @package HeroQR\Customs
 */
class MarkerPaths extends AbstractCustomPaths
{
    public const M1 = __DIR__ . '/../../assets/Markers/Marker-1.png';
    public const M2 = __DIR__ . '/../../assets/Markers/Marker-2.png';
    public const M3 = __DIR__ . '/../../assets/Markers/Marker-3.png';
    public const M4 = __DIR__ . '/../../assets/Markers/Marker-4.png';
    public const M5 = __DIR__ . '/../../assets/Markers/Marker-5.png';
    public const M6 = __DIR__ . '/../../assets/Markers/Marker-6.png';

    /**
     * Retrieves all marker paths as an associative array
     */
    public static function getAllPaths(): array
    {
        $reflection = new \ReflectionClass(static::class);
        $constants = $reflection->getConstants();

        return array_filter($constants, fn($key) => str_starts_with($key, 'M'), ARRAY_FILTER_USE_KEY);
    }
}
