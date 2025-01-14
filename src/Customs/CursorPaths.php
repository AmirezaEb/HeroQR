<?php

namespace HeroQR\Customs;

/**
 * This class manages a collection of cursor paths, providing methods to retrieve paths by key 
 * and validate whether a given key corresponds to a valid cursor
 */

class CursorPaths
{
    public const C1 = __DIR__ . '/../../assets/Cursors/Cursor-1.png';
    public const C2 = __DIR__ . '/../../assets/Cursors/Cursor-2.png';
    public const C3 = __DIR__ . '/../../assets/Cursors/Cursor-3.png';


    /**
     * Retrieves all marker paths as an associative array
     */
    public static function getAllPaths(): array
    {
        return [
            'C1' => self::C1,
            'C2' => self::C2,
            'C3' => self::C3,
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
