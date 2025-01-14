<?php

namespace HeroQR\Customs;

/**
 * A class that manages paths for marker images, providing methods to retrieve paths, 
 * validate keys, and get the specific path for a marker based on its key
 */

class MarkerPaths
{
    public const M1 = __DIR__ . '/../../assets/Markers/Marker-1.png';
    public const M2 = __DIR__ . '/../../assets/Markers/Marker-2.png';
    public const M3 = __DIR__ . '/../../assets/Markers/Marker-3.png';

    /**
     * Retrieves all marker paths as an associative array
     */
    public static function getAllPaths(): array
    {
        return [
            'M1' => self::M1,
            'M2' => self::M2,
            'M3' => self::M3,
        ];
    }

    /**
     * Checks if the given key is a valid marker key
     *
     * @param string $key
     * @return bool
     */
    public static function isValidKey(string $key): bool
    {
        $allKeys = array_keys(self::getAllPaths());
        return in_array($key, $allKeys, true);
    }

    /**
     * Retrieves the path for a specific marker key
     *
     * @param string $key
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getPath(string $key): string
    {
        $paths = self::getAllPaths();

        if (!isset($paths[$key])) {
            throw new \InvalidArgumentException("Invalid marker key: {$key}");
        }

        return $paths[$key];
    }
}
