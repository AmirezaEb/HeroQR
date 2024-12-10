<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Text
 *
 * This class validates plain text to ensure it does not contain any unsafe content.
 * It checks the text for common security vulnerabilities, specifically:
 *   - Script tags (to prevent XSS attacks).
 *   - SQL injection patterns (to prevent malicious database queries).
 *
 * If any of these vulnerabilities are detected, the text is considered invalid.
 *
 * Example of usage:
 *   Text::validate("Sample text here");
 *
 * @package HeroQR\DataTypes
 */

class Text extends AbstractDataType
{
    public static function validate(string $value): bool
    {
        if (self::hasScriptTag($value) || self::hasSqlInjection($value)) {
            return false;
        }

        return true;
    }
}
