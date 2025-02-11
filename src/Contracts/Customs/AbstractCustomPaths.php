<?php

namespace HeroQR\Contracts\Customs;

/**
 * Abstract class for managing custom marker paths.
 * 
 * This class provides the base functionality for handling marker paths, 
 * including methods for retrieving paths, validating keys, and checking for valid markers.
 * Subclasses will implement the logic for storing and handling custom marker paths.
 * 
 * @package HeroQR\Contracts\Customs
 */

abstract class AbstractCustomPaths
{
    /**
     * Returns all paths as an associative array
     */
    abstract public static function getAllPaths(): array;

    /**
     * Checks if the given key is a valid marker key
     *
     * @param string $key
     * @return bool
     */
    public static function isValidKey(string $key): bool
    {
        $allKeys = array_keys(static::getAllPaths());
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
        $paths = static::getAllPaths();

        if (!isset($paths[$key])) {
            throw new \InvalidArgumentException("Invalid marker key: {$key}");
        }

        return $paths[$key];
    }

    /**
     * Retrieves the value of a constant based on its key
     * 
     * @param string $key
     * @return mixed
     */
    public static function getValueByKey(string $key): ?string
    {
        if (!static::isValidKey($key)) {
            $validKeys = implode(', ', array_keys(static::getAllPaths()));
            throw new \InvalidArgumentException(
                "Invalid key '{$key}' provided. Valid keys are : {$validKeys}."
            );
        }
        
        $paths = static::getAllPaths();
        return $paths[$key] ?? null;
    }
}
