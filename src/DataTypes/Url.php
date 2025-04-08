<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Url
 *
 * This class validates a URL to ensure it follows a proper structure and does not contain unsafe content.
 * It checks the URL's validity by verifying the following:
 *   - The URL format (http/https).
 *   - The URL contains a valid domain (host).
 *   - The URL is not malicious, ensuring no SQL injections, script tags, or relative path traversals.
 *
 * The URL is first checked using PHP's filter_var function, then it ensures that:
 *   - The URL follows a valid format using a regular expression (regex).
 *   - The host part of the URL is a valid IP address or a domain.
 *   - It does not contain potentially harmful content, such as SQL injection patterns or script tags.
 *   - It does not attempt relative path traversal (e.g., '../').
 *
 * Example valid URL:
 *   - https://www.example.com
 *
 * Example invalid URL:
 *   - http://localhost (does not contain a valid domain or external host).
 *
 * @package HeroQR\DataTypes
 */

class Url extends AbstractDataType
{
    public static function validate(string $url): bool
    {
        $parsedUrl = parse_url($url);

        if (!filter_var($url, FILTER_VALIDATE_URL) || empty($parsedUrl['host'])) {
            return false;
        }

        if (
            !preg_match('/^(https?:\/\/(?:[a-zA-Z0-9-]+\.)+[a-zA-Z0-9-]+(?:\/[^\s]*)?(\?[^\s]*)?(#\S*)?)$/i', $url)
            && !filter_var($url, FILTER_VALIDATE_IP)
        ) {
            return false;
        }

        if (self::hasSqlInjection($url) || self::hasScriptTag($url) || preg_match('/(\.\.\/)/', $url)) {
            return false;
        }

        return true;
    }
}
