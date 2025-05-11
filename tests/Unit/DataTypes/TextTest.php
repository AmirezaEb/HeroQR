<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Text;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class TextTest
 * Tests the Text class.
 */
class TextTest extends TestCase
{
    /**
     * Test validating text with various inputs
     */
    #[Test]
    #[DataProvider('textProvider')]
    public function validateText(string $text, bool $expected): void
    {
        $this->assertSame($expected, Text::validate($text), 'Text validation failed for: ' . $text);
    }

    /**
     * Provides a list of text inputs and expected validation result
     */
    public static function textProvider(): array
    {
        return [
            'Valid text without unsafe content'              => ['Example Test | متن تست', true],
            'Text with script tag (XSS attack)'               => ["<script>alert('XSS');</script>", false],
            'Text with SQL injection patterns'                => ['SELECT * FROM users WHERE id = 1; --', false],
            'Text with both script tag and SQL injection'     => ["<script>alert('XSS');</script>SELECT * FROM users WHERE id = 1; --", false],
        ];
    }
}
