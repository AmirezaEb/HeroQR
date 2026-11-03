<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\TextValidator;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class TextTest
 * Tests the Text class.
 */
class TextValidatorTest extends TestCase
{
    /**
     * Provides a list of text inputs and expected validation result
     */
    public static function textProvider(): array
    {
        return [
            'Valid text without unsafe content' => ['Example Test | متن تست', true],
            'Text with script tag (XSS attack)' => ["<script>alert('XSS');</script>", false],
            'Text with SQL injection patterns' => ['SELECT * FROM users WHERE id = 1; --', false],
            'Text with both script tag and SQL injection' => ["<script>alert('XSS');</script>SELECT * FROM users WHERE id = 1; --", false],
            'Valid short text' => ['Hi', true],
            'Valid numbers and symbols' => ['Order #12345 - total: 99.50$', true],
            'Valid multiline-like spaces' => ['Line one and line two', true],
            'Valid empty after trim is still text without blacklist' => ['safe content', true],
            'Invalid INSERT keyword' => ['Please INSERT into table', false],
            'Invalid DROP keyword' => ['DROP TABLE users', false],
            'Invalid UNION keyword' => ['find UNION all rows', false],
            'Invalid comment injection' => ['value -- comment', false],
            'Invalid block comment start' => ['value /* secret', false],
            'Invalid script with attributes' => ['<script type="text/javascript">alert(1)</script>', false],
        ];
    }

    /**
     * Test that getType returns TextValidator class
     */
    #[Test]
    public function isGetTypeReturnsTextValidator(): void
    {
        $this->assertSame(TextValidator::class, TextValidator::getType());
    }


    /**
     * Tests validating text with various inputs
     */
    #[Test]
    #[DataProvider('textProvider')]
    public function isTextValid(string $text, bool $expected): void
    {
        $this->assertSame($expected, TextValidator::validate($text), 'Text validation failed for: ' . $text);
    }
}
