<?php

declare(strict_types=1);

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
     *
     * @return array
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
        return array_key_exists($key, static::getAllPaths());
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
            throw new \InvalidArgumentException("Invalid marker key : {$key}");
        }

        return $paths[$key];
    }

    /**
     * Retrieves the value of a constant based on its key
     *
     * @param string $key
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public static function getValueByKey(string $key): ?string
    {
        $paths = static::getAllPaths();

        if (!isset($paths[$key])) {
            throw new \InvalidArgumentException(
                "Invalid key '{$key}' provided. Valid keys are : " . implode(', ', array_keys($paths)) . "."
            );
        }

        return $paths[$key];
    }
}
