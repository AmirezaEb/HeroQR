<?php

namespace HeroQR\Customs;

use HeroQR\Contracts\Customs\AbstractCustomPaths;

/**
 * This class manages a collection of cursor paths, providing methods to retrieve paths by key
 * and validate whether a given key corresponds to a valid cursor
 */
class CursorPaths extends AbstractCustomPaths
{
    private const PATH = __DIR__ . '/../../assets/Cursors/';

    /**
     * Retrieves all marker paths as an associative array
     *
     * @return array<string, string>
     */
    public static function getAllPaths(): array
    {
        return [
            'C1' => self::PATH . 'Cursor-1.png',
            'C2' => self::PATH . 'Cursor-2.png',
            'C3' => self::PATH . 'Cursor-3.png',
            'C4' => self::PATH . 'Cursor-4.png',
            'C5' => self::PATH . 'Cursor-5.png',
            'C6' => self::PATH . 'Cursor-6.png',
            'C7' => self::PATH . 'Cursor-7.png',
        ];
    }
}