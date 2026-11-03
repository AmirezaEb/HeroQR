<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\{
    DataType,
    EmailValidator,
    LocationValidator,
    NoneValidator,
    PhoneValidator,
    TextValidator,
    UrlValidator,
    WifiValidator
};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class ValidatorTypeTest
 * Tests getType() across all validator classes and DataType enum mapping.
 */
class ValidatorTypeTest extends TestCase
{
    /**
     * Provides validator classes and their expected getType values
     */
    public static function validatorTypeProvider(): array
    {
        return [
            'None' => [NoneValidator::class],
            'Text' => [TextValidator::class],
            'Url' => [UrlValidator::class],
            'Email' => [EmailValidator::class],
            'Phone' => [PhoneValidator::class],
            'Wifi' => [WifiValidator::class],
            'Location' => [LocationValidator::class],
        ];
    }

    /**
     * Tests that getType returns the concrete validator class name
     */
    #[Test]
    #[DataProvider('validatorTypeProvider')]
    public function isGetTypeMatchesClass(string $validatorClass): void
    {
        $this->assertSame($validatorClass, $validatorClass::getType());
    }

    /**
     * Provides DataType enum cases and expected validator classes
     */
    public static function dataTypeCaseProvider(): array
    {
        return [
            'None maps to NoneValidator' => [DataType::None, NoneValidator::class],
            'Text maps to TextValidator' => [DataType::Text, TextValidator::class],
            'Url maps to UrlValidator' => [DataType::Url, UrlValidator::class],
            'Email maps to EmailValidator' => [DataType::Email, EmailValidator::class],
            'Phone maps to PhoneValidator' => [DataType::Phone, PhoneValidator::class],
            'Wifi maps to WifiValidator' => [DataType::Wifi, WifiValidator::class],
            'Location maps to LocationValidator' => [DataType::Location, LocationValidator::class],
        ];
    }

    /**
     * Tests that each DataType enum case points to the correct validator class
     */
    #[Test]
    #[DataProvider('dataTypeCaseProvider')]
    public function isDataTypeMapsToValidator(DataType $type, string $expectedClass): void
    {
        $this->assertSame($expectedClass, $type->value);
        $this->assertTrue(class_exists($type->value));
    }

    /**
     * Test that DataType enum has exactly seven cases
     */
    #[Test]
    public function isDataTypeHasExpectedCaseCount(): void
    {
        $this->assertCount(7, DataType::cases());
    }
}
