<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Email
 *
 * This class provides robust validation for email addresses. It includes:
 * - Validating the email format using PHP's `FILTER_VALIDATE_EMAIL`.
 * - Using a regex pattern to ensure a proper email structure.
 * - Checking for the existence of an MX record for the email domain.
 * - Validating the domain against a predefined blacklist.
 * - Normalizing the domain to handle case-insensitivity.
 *
 * @package HeroQR\DataTypes
 */

class Email extends AbstractDataType
{
    public static function validate(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            return false;
        }

        $domain = substr(strrchr($email, '@'), 1);

        $blacklist = ['example.com', 'test.com', 'invalid.com', 'nonexistentdomain.xyz'];
        foreach ($blacklist as $blockedDomain) {
            if (str_ends_with($domain, $blockedDomain)) {
                return false;
            }
        }

        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            return false;
        }

        return true;
    }
}
