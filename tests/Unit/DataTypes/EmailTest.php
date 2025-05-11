<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Email;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class EmailTest
 * Tests the Email class
 */
class EmailTest extends TestCase
{
    /*
     * Provides a list of emails and expected validation results
     */
    public static function emailProvider(): array
    {
        return [
            # Valid emails
            'Valid - Yahoo' => ['abcdef@yahoo.com', true],
            'Valid - Org domain' => ['user@example.org', true],
            'Valid - Gmail' => ['john.doe@gmail.com', true],
            'Valid - HeroExpert' => ['info@heroexpert.ir', true],
            'Valid - Short email' => ['a@b.co', true],
            'Valid - Dashes & digits' => ['j-doe1234@domain.com', true],
            'Valid - Alias' => ['email+alias@domain.com', true],
            'Valid - Personal' => ['aabrahimi1718@gmail.com', true],

            # Invalid emails
            'Invalid - Plain' => ['plainaddress', false],
            'Invalid - Missing TLD' => ['missing@tld', false],
            'Invalid - No local part' => ['@missinglocalpart.com', false],
            'Invalid - No domain name' => ['missingdomain@.com', false],
            'Invalid - No @ symbol' => ['missingat.com', false],
            'Invalid - Dot start domain' => ['user@.com', false],
            'Invalid - No dot in domain' => ['user@com', false],
            'Invalid - Dash in domain' => ['email@-domain.com', false],
            'Invalid - Double dots' => ['email@domain..com', false],
            'Invalid - Trailing space' => ['email@domain.com ', false],
            'Invalid - Leading space' => [' email@domain.com', false],

            # Blacklisted domains
            'Blacklisted - example' => ['user@example.com', false],
            'Blacklisted - test.com' => ['admin@test.com', false],
            'Blacklisted - invalid.com' => ['contact@invalid.com', false],

            # Non-existing domains
            'Non-existent - xyz' => ['user@nonexistentdomain.xyz', false],
            'Non-existent - fake' => ['user@fake-domain.abc', false],
        ];
    }

    /**
     * Tests email validation using various valid, invalid, blacklisted, and fake domain emails
     */
    #[Test]
    #[DataProvider('emailProvider')]
    public function testEmailValidation(string $email, bool $expected): void
    {
        $result = Email::validate($email);
        $this->assertSame($expected, $result, "Email validation failed for: '$email'");
    }
}
