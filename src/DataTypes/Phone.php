<?php

namespace HeroQR\DataTypes;

use libphonenumber\PhoneNumberUtil;
use HeroQR\Contracts\DataTypes\AbstractDataType;

// use libphonenumber\PhoneNumberUtil;

/**
 * Class Phone
 *
 * This class validates a phone number to ensure it follows a valid format.
 * It uses the Google libphonenumber library to parse and validate the phone number.
 * The library checks for a correct structure based on international phone number standards.
 *
 * The `validate` method will return true if the phone number is valid and false otherwise.
 *
 * Example of usage:
 *   Phone::validate("+1234567890");
 *
 * @package HeroQR\DataTypes
 */

class Phone extends AbstractDataType
{
    public static function validate(string $phone): bool
    {
        $className = 'libphonenumber\PhoneNumberUtil';

        if (!class_exists($className)) {
            throw new \RuntimeException('The library "<a href="https://github.com/giggsey/libphonenumber-for-php" target="_blank" style="text-decoration: none;">giggsey/libphonenumber-for-php</a>" is required for phone number validation. Please install it using "composer require giggsey/libphonenumber-for-php".');
        }
        
        
        $phoneNumberUtil = $className::getInstance();

        $phoneNumber = $phoneNumberUtil->parse($phone, null);

        return (bool)$phoneNumber;
    }
}