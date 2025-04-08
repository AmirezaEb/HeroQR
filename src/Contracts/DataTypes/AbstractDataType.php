<?php

namespace HeroQR\Contracts\DataTypes;

/**
 * Abstract class AbstractDataType
 * 
 * This abstract class defines the structure for handling different types of data,
 * including validation, conversion to array or string, and security checks such as 
 * preventing SQL injection and detecting script tags.
 * 
 * @package HeroQR\Contracts\DataTypes
 */

abstract class AbstractDataType
{

    /**
     * Validate the given value
     *
     * @param string $value The value to validate
     * @return bool True if the value is valid, false otherwise
     */
    abstract public static function validate(string $value): bool;

    /**
     * Get the type of the validator
     *
     * @return string The class name of the validator
     */
    public static function getType(): string
    {
        return static::class;
    }

    /**
     * Convert the value to an array with additional data
     *
     * @param string $value The value to convert
     * @param array $additionalData Additional data to include in the array
     * @return array The converted array
     */
    protected static function toArray(string $value, array $additionalData = []): array
    {
        $data = ['value' => $value];

        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        return $data;
    }

    /**
     * Convert the value to a string
     *
     * @param string $value The value to convert
     * @return string The converted string
     */
    protected static function toString(string $value): string
    {
        return $value;
    }

    /**
     * Checks for the presence of SQL-specific keywords to prevent SQL Injection attacks
     *
     * @param string $value The input data to be checked
     * @return bool Returns true if any SQL keywords are found
     */
    protected static function hasSqlInjection(string $value): bool
    {
        $blacklist = ['SELECT', 'INSERT', 'DROP', 'UNION', '--', ';', '/*', '*/', '*'];

        foreach ($blacklist as $keyword) {
            if (stripos($value, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks for the presence of script tags in the data.
     *
     * @param string $value The URL to check.
     * @return bool Returns true if script tags are found.
     */
    protected static function hasScriptTag(string $value): bool
    {
        return preg_match('/<script.*?>.*?<\/script>/is', $value) === 1;
    }
}