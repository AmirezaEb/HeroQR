<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Phone;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class PhoneTest
 * Tests the Phone class
 */
class PhoneTest extends TestCase
{
    /*
     * Provides a list of phone numbers and expected results (true for valid, false for invalid)
     */
    public static function phoneNumbersProvider(): array
    {
        return [
            'Valid Iran'          => ['+98 935 891 92 79', true],
            'Valid US'            => ['+1 123 456 7890', true],
            'Valid UK'            => ['+442012345678', true],
            'Valid Australia'     => ['+61212345678', true],
            'Valid Italy'         => ['+390212345678', true],
            'Valid India'         => ['+91 12345 67890', true],
            'Invalid no plus'     => ['989358919279', false],
            'Invalid unknown code'=> ['+89358919279', false],
            'Invalid local format'=> ['90212345678', false],
            'Invalid No Country Code' => ['555123456789', false]
        ];
    }

    /**
     * Tests phone number validation using various valid and invalid numbers
     */
    #[Test]
    #[DataProvider('phoneNumbersProvider')]
    public function PhoneValidation(string $number, bool $expected): void
    {
        if (!class_exists(\libphonenumber\PhoneNumberUtil::class)) {
            $this->expectException(\RuntimeException::class);
            Phone::validate($number);
            return;
        }

        try {
            $result = Phone::validate($number);
        } catch (\libphonenumber\NumberParseException) {
            $result = false;
        }

        $this->assertSame($expected, $result, "Phone number validation failed for: $number");
    }
}