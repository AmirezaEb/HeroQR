<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Url
 *
 * This class validates a URL to ensure it follows proper structure and does not contain unsafe content.
 * It checks if the URL is valid, if the host has a valid domain, and ensures that the URL does not contain
 * common security vulnerabilities such as SQL injection, script tags, or relative path traversals.
 *
 * It performs the following checks:
 *   - Validates the structure of the URL using PHP's filter_var function.
 *   - Ensures the host part has a valid domain with a top-level domain.
 *   - Checks for common security issues like SQL injection, script tags, and relative path traversal.
 *
 * Example of a valid URL: https://www.example.com
 *
 * @package HeroQR\DataTypes
 */

class Url extends AbstractDataType
{
    public static function validate(string $url): bool
    {
        $parsedUrl = parse_url($url);

        if (!filter_var($url, FILTER_VALIDATE_URL) || !isset($parsedUrl['host'])) {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $parsedUrl['host']) 
            && !filter_var($parsedUrl['host'], FILTER_VALIDATE_IP)) {
            return false;
        }

        if (self::hasSqlInjection($url) || self::hasScriptTag($url) || preg_match('/(\.\.\/)/', $url)) {
            return false;
        }

        if (!filter_var($parsedUrl['host'], FILTER_VALIDATE_IP) 
            && !checkdnsrr($parsedUrl['host'], 'A') 
            && !checkdnsrr($parsedUrl['host'], 'CNAME')) {
            return false;
        }

        return true;
    }
}
