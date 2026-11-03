<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\UrlValidator;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class UrlTest
 * Tests the Url class.
 */
class UrlValidatorTest extends TestCase
{
    /**
     * Provides a list of general URLs and their expected validation results
     */
    public static function urlsTestingProvider(): array
    {
        return [
            'Valid .museum domain' => ['https://www.example.museum', true],
            'Valid path' => ['https://example.com/path/to/page', true],
            'Valid subdomain' => ['https://sub.example.com', true],
            'Invalid scheme' => ['htt://www.example.co.uk', false],
            'Only domain without scheme' => ['example.com', false],
            'Missing scheme with www' => ['www.example.com', false],
            'FTP scheme not allowed' => ['ftp://example.com', false],
            'Invalid characters in domain' => ['https://exa mple.com', false],
            'URL with @ in domain' => ['https://example@domain.com', false],
            'Domain ends with dot' => ['https://www.invalid-url.', false],
            'Illegal characters in URL' => ['http://heroexpert.ir*?>', false],
        ];
    }

    /**
     * Tests URL validation with multiple general cases
     */
    #[Test]
    #[DataProvider('urlsTestingProvider')]
    public function isValidUrl(string $url, bool $expected): void
    {
        $this->assertSame($expected, UrlValidator::validate($url), 'URL validation failed for: ' . $url);
    }

    /**
     * URL with long query string
     */
    #[Test]
    public function isValidUrlWithLongQueryString(): void
    {
        $url = 'https://example.com/search?' . str_repeat('q=valid&', 100);
        $this->assertTrue(UrlValidator::validate($url), 'Valid URL with long query string failed validation');
    }

    /**
     * Test URL without security issues
     */
    #[Test]
    public function isValidUrlWithoutSecurityIssues(): void
    {
        $url = 'https://www.example.com/path/to/resource?query=valid';
        $this->assertTrue(UrlValidator::validate($url), 'Valid URL without security issues failed validation');
    }

    /**
     * Test URL with IP address
     */
    #[Test]
    public function isValidUrlWithIpAddress(): void
    {
        $url = 'http://127.0.0.1';
        $this->assertTrue(UrlValidator::validate($url), 'Valid URL with IP address failed validation');
    }

    /**
     * Test URL with SQL injection
     */
    #[Test]
    public function isInvalidUrlWithSqlInjection(): void
    {
        $url = 'https://example.com/?search=union+select';
        $this->assertFalse(UrlValidator::validate($url), 'URL containing SQL injection passed');
    }

    /**
     * Test URL with script tag
     */
    #[Test]
    public function isInvalidUrlWithScriptTag(): void
    {
        $url = 'https://example.com/?search=<script>alert("xss")</script>';
        $this->assertFalse(UrlValidator::validate($url), 'URL containing script tag passed');
    }

    /**
     * Test URL with path traversal
     */
    #[Test]
    public function isInvalidUrlWithPathTraversal(): void
    {
        $url = 'https://example.com/../../etc/passwd';
        $this->assertFalse(UrlValidator::validate($url), 'URL with path traversal passed');
    }

    /**
     * Test URL with only protocol
     */
    #[Test]
    public function isInvalidUrlWithOnlyProtocol(): void
    {
        $url = 'https://';
        $this->assertFalse(UrlValidator::validate($url), 'URL with only protocol passed validation');
    }

    /**
     * Test empty URL
     */
    #[Test]
    public function isInvalidEmptyUrl(): void
    {
        $url = '';
        $this->assertFalse(UrlValidator::validate($url), 'Empty URL passed validation');
    }

    /**
     * Test URL with fragment
     */
    #[Test]
    public function isValidUrlWithFragment(): void
    {
        $url = 'https://example.com/page#section';
        $this->assertTrue(UrlValidator::validate($url), 'Valid URL with fragment failed validation');
    }

    /**
     * Test URL with port is rejected by the current URL pattern
     */
    #[Test]
    public function isInvalidUrlWithPort(): void
    {
        $url = 'https://example.com:8080/api';
        $this->assertFalse(UrlValidator::validate($url), 'URL with port should fail current validation pattern');
    }

    /**
     * Test HTTP URL
     */
    #[Test]
    public function isValidHttpUrl(): void
    {
        $url = 'http://example.com';
        $this->assertTrue(UrlValidator::validate($url), 'Valid HTTP URL failed validation');
    }

    /**
     * Test URL with INSERT SQL keyword
     */
    #[Test]
    public function isInvalidUrlWithInsertKeyword(): void
    {
        $url = 'https://example.com/?q=INSERT+INTO';
        $this->assertFalse(UrlValidator::validate($url), 'URL containing INSERT keyword passed');
    }

    /**
     * Test URL with DROP SQL keyword
     */
    #[Test]
    public function isInvalidUrlWithDropKeyword(): void
    {
        $url = 'https://example.com/?q=DROP+TABLE';
        $this->assertFalse(UrlValidator::validate($url), 'URL containing DROP keyword passed');
    }

    /**
     * Test that getType returns UrlValidator class
     */
    #[Test]
    public function isGetTypeReturnsUrlValidator(): void
    {
        $this->assertSame(UrlValidator::class, UrlValidator::getType());
    }
}

