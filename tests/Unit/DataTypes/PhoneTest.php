<?php

namespace HeroQR\Tests\Unit\DataTypes;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use HeroQR\DataTypes\Phone;
use RuntimeException;

/**
 * Class PhoneTest
 * Tests the Phone class.
 */
class PhoneTest extends TestCase
{
    /**
     * Test for validating valid phone numbers
     */
    #[Test]
    public function isValidPhone(): void
    {
        $numbers = [
            '+98 935 891 92 79',
            '+1 123 456 7890',
            '+442012345678',
            '+61212345678',
            '+390212345678',
            '+91 12345 67890'
        ];

        $className = 'libphonenumber\PhoneNumberUtil';

        if (!class_exists($className)) {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('The library "<a href="https://github.com/giggsey/libphonenumber-for-php" target="_blank" style="text-decoration: none;">giggsey/libphonenumber-for-php</a>" is required for phone number validation. Please install it using "composer require giggsey/libphonenumber-for-php".');
            $result = Phone::validate('+989358919279'); 
        } else {
            foreach ($numbers as $number) {
                $result = Phone::validate($number);
                $this->assertTrue($result, "Phone number $number should be valid");
            }
        }
    }

    /**
     * Test for validating invalid phone numbers
     */
    #[Test]
    public function isInvalidPhone(): void
    {
        $className = 'libphonenumber\PhoneNumberUtil';

        if (!class_exists($className)) {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('The library "<a href="https://github.com/giggsey/libphonenumber-for-php" target="_blank" style="text-decoration: none;">giggsey/libphonenumber-for-php</a>" is required for phone number validation. Please install it using "composer require giggsey/libphonenumber-for-php".');
            $result = Phone::validate('989358919279');
        } else {
            $numbers = ['989358919279', '+89358919279', '90212345678', '+39021234567'];

            foreach ($numbers as $number) {
                $this->expectException(\libphonenumber\NumberParseException::class);
                $this->expectExceptionMessage('Missing or invalid default region');

                $result = Phone::validate($number);

                $this->assertFalse($result, "Phone number $number should be invalid.");
            }
        }
    }
}