<?php 

namespace HeroQR\Contracts\DataTypes;

abstract class AbstractDataType
{
    abstract public static function validate(string $value): bool;

    public static function getType(): string
    {
        return static::class;
    }

    protected static function toArray(string $value, array $additionalData = []): array
    {
        $data = ['value' => $value];

        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }

        return $data;
    }

    protected static function toString(string $value): string
    {
        return (string)$value;
    }

    /**
     * Checks for the presence of SQL-specific keywords to prevent SQL Injection attacks.
     *
     * @param string $value The input data to be checked.
     * @return bool Returns true if any SQL keywords are found.
     */
    protected static function hasSqlInjection(string $value): bool
    {
        $blacklist = ['SELECT', 'INSERT', 'DROP', 'UNION', '--', ';', '/*', '*/'];

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
