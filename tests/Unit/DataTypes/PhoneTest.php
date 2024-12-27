<?php

namespace HeroQR\Tests\DataTypes;

use HeroQR\DataTypes\Phone;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
            '+98 935 891 92 79', '+1 123 456 7890', '+442012345678',
            '+61212345678', '+390212345678', '+91 12345 67890'
        ];

        foreach ($numbers as $number) {
            $result = Phone::validate($number);
            $this->assertTrue($result, "Phone number $number should be valid");
        }
    }

    /**
     * Test for validating invalid phone numbers
     */
    #[Test]
    public function isInvalidPhone(): void
    {
        $numbers = ['989358919279', '+89358919279', '90212345678','+39021234567'];

        foreach ($numbers as $number) {
            $this->expectException(\libphonenumber\NumberParseException::class);
            $this->expectExceptionMessage('Missing or invalid default region');

            $result = Phone::validate($number);

            $this->assertFalse($result, "Phone number $number should be invalid.");
        }
    }
}