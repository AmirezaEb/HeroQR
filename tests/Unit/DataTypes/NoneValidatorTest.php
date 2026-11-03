<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\NoneValidator;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class NoneValidatorTest
 * Tests the NoneValidator class.
 */
class NoneValidatorTest extends TestCase
{
    /**
     * Provides various inputs that NoneValidator should always accept
     */
    public static function noneDataProvider(): array
    {
        return [
            'Empty string' => ['', true],
            'Plain text' => ['Hello HeroQR', true],
            'URL-like string' => ['https://example.com', true],
            'Email-like string' => ['user@example.com', true],
            'Script content allowed' => ["<script>alert('xss')</script>", true],
            'SQL-like content allowed' => ['SELECT * FROM users;', true],
            'Whitespace only' => ['   ', true],
            'Unicode text' => ['متن فارسی | 日本語', true],
        ];
    }

    /**
     * Tests that NoneValidator always returns true regardless of content
     */
    #[Test]
    #[DataProvider('noneDataProvider')]
    public function isNoneAlwaysValid(string $data, bool $expected): void
    {
        $this->assertSame($expected, NoneValidator::validate($data), 'NoneValidator should accept: ' . $data);
    }

    /**
     * Test that getType returns the NoneValidator class name
     */
    #[Test]
    public function isGetTypeReturnsClassName(): void
    {
        $this->assertSame(NoneValidator::class, NoneValidator::getType());
    }
}
