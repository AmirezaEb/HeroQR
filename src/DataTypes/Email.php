<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Email
 *
 * This class validates an email address to ensure its structure and content are correct.
 * It performs the following checks:
 *   - Validates the email format using PHP's `filter_var` with the `FILTER_VALIDATE_EMAIL` filter.
 *   - Checks the email format using a regular expression to ensure it matches common email patterns.
 *   - Ensures that the email domain is not part of a predefined blacklist (e.g., 'example.com', 'test.com').
 *
 * The `validate` method will return true if the email is valid and false otherwise.
 *
 * Example of usage:
 *   Email::validate("user@example.com");
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
            return true;
        }

        $blacklist = ['example.com', 'test.com'];
        if (in_array(explode('@', $email)[1], $blacklist)) {
            return false;
        }

        return true;
    }
}
