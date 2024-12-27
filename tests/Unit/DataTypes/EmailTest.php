<?php

namespace HeroQR\Tests\DataTypes;

use HeroQR\DataTypes\Email;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class EmailTest
 * Tests the Email class.
 */
class EmailTest extends TestCase
{
    /**
     * Test valid email addresses
     */
    #[Test]
    public function isValidEmails(): void
    {
        $validEmails = [
            'abcdef@yahoo.com',
            'user@example.org',
            'john.doe@gmail.com',
            'aabrahimi1718@gmail.com',
            'a@b.co',
            'info@heroexpert.ir',
            'j-doe1234@domain.com',
            'email+alias@domain.com',
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(Email::validate($email), "Valid email '$email' failed");
        }
    }

    /**
     * Test invalid email addresses
     */
    #[Test]
    public function isInvalidEmails(): void
    {
        $invalidEmails = [
            'plainaddress',
            'missing@tld',
            '@missinglocalpart.com',
            'missingdomain@.com',
            'missingat.com',
            'user@.com',
            'user@com',
            'email@-domain.com',
            'email@domain..com',
            'email@domain.com ',
            ' email@domain.com',
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(Email::validate($email), "Invalid email '$email' passed");
        }
    }

    /**
     * Test emails with domains in the blacklist
     */
    #[Test]
    public function isEmailsInBlacklist(): void
    {
        $blacklistedEmails = [
            'user@example.com',
            'admin@test.com',
            'contact@invalid.com',
        ];

        foreach ($blacklistedEmails as $email) {
            $this->assertFalse(Email::validate($email), "Blacklisted email '$email' passed");
        }
    }

    /**
     * Test emails with non-existing domains
     */
    #[Test]
    public function isEmailsWithNonExistingDomains(): void
    {
        $nonExistingDomainEmails = [
            'user@nonexistentdomain.xyz',
            'user@fake-domain.abc',
        ];

        foreach ($nonExistingDomainEmails as $email) {
            $this->assertFalse(Email::validate($email), "Non-existent domain email '$email' passed");
        }
    }
}
