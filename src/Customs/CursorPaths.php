<?php

namespace HeroQR\Customs;

use HeroQR\Contracts\Customs\AbstractCustomPaths;

/**
 * This class manages a collection of cursor paths, providing methods to retrieve paths by key
 * and validate whether a given key corresponds to a valid cursor
 *
 * @package HeroQR\Customs
 */
class CursorPaths extends AbstractCustomPaths
{
    public const C1 = __DIR__ . '/../../assets/Cursors/Cursor-1.png';
    public const C2 = __DIR__ . '/../../assets/Cursors/Cursor-2.png';
    public const C3 = __DIR__ . '/../../assets/Cursors/Cursor-3.png';
    public const C4 = __DIR__ . '/../../assets/Cursors/Cursor-4.png';
    public const C5 = __DIR__ . '/../../assets/Cursors/Cursor-5.png';
    public const C6 = __DIR__ . '/../../assets/Cursors/Cursor-6.png';


    /**
     * Retrieves all marker paths as an associative array
     */
    public static function getAllPaths(): array
    {
        $reflection = new \ReflectionClass(static::class);
        $constants = $reflection->getConstants();

        return array_filter($constants, fn($key) => str_starts_with($key, 'C'), ARRAY_FILTER_USE_KEY);
    }
}
