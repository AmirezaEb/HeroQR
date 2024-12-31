<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Text;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class TextTest
 * Tests the Text class.
 */
class TextTest extends TestCase
{
    /**
     * Test for validating text that contains no unsafe content
     */
    #[Test]
    public function isValidText(): void
    {
        $text = 'Example Test | متن تست';
        $result = Text::validate($text);
        $this->assertTrue($result, 'Text should be valid as it contains no unsafe content');
    }

    /**
     * Test for validating text that contains a script tag (XSS attack)
     */
    #[Test]
    public function isTextWithScriptTag(): void
    {
        $text = "<script>alert('XSS');</script>";
        $result = Text::validate($text);
        $this->assertFalse($result, 'Text should be invalid as it contains a script tag');
    }

    /**
     * Test for validating text that contains SQL injection patterns
     */
    #[Test]
    public function isTextWithSqlInjection(): void
    {
        $text = 'SELECT * FROM users WHERE id = 1; --';
        $result = Text::validate($text);
        $this->assertFalse($result, 'Text should be invalid as it contains SQL injection patterns');
    }

    /**
     * This text should fail the validation
     */
    #[Test]
    public function isTextWithScriptAndSqlInjection(): void
    {
        $text = "<script>alert('XSS');</script>SELECT * FROM users WHERE id = 1; --";
        $result = Text::validate($text);
        $this->assertFalse($result, 'Text should be invalid as it contains both a script tag and SQL injection patterns');
    }
}
