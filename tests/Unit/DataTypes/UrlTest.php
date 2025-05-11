<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Url;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class UrlTest
 * Tests the Url class.
 */
class UrlTest extends TestCase
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
     * Test URL validation with multiple general cases
     */
    #[Test]
    #[DataProvider('urlsTestingProvider')]
    public function isValidUrl(string $url, bool $expected): void
    {
        $this->assertSame($expected, Url::validate($url), 'URL validation failed for: ' . $url);
    }

    /**
     * Test URL with long query string
     */
    #[Test]
    public function isValidUrlWithLongQueryString(): void
    {
        $url = 'https://example.com/search?' . str_repeat('q=valid&', 100);
        $this->assertTrue(Url::validate($url), 'Valid URL with long query string failed validation');
    }

    /**
     * Test URL without security issues
     */
    #[Test]
    public function isValidUrlWithoutSecurityIssues(): void
    {
        $url = 'https://www.example.com/path/to/resource?query=valid';
        $this->assertTrue(Url::validate($url), 'Valid URL without security issues failed validation');
    }

    /**
     * Test URL with IP address
     */
    #[Test]
    public function isValidUrlWithIpAddress(): void
    {
        $url = 'http://127.0.0.1';
        $this->assertTrue(Url::validate($url), 'Valid URL with IP address failed validation');
    }

    /**
     * Test URL with SQL injection
     */
    #[Test]
    public function isInvalidUrlWithSqlInjection(): void
    {
        $url = 'https://example.com/?search=union+select';
        $this->assertFalse(Url::validate($url), 'URL containing SQL injection passed');
    }

    /**
     * Test URL with script tag
     */
    #[Test]
    public function isInvalidUrlWithScriptTag(): void
    {
        $url = 'https://example.com/?search=<script>alert("xss")</script>';
        $this->assertFalse(Url::validate($url), 'URL containing script tag passed');
    }

    /**
     * Test URL with path traversal
     */
    #[Test]
    public function isInvalidUrlWithPathTraversal(): void
    {
        $url = 'https://example.com/../../etc/passwd';
        $this->assertFalse(Url::validate($url), 'URL with path traversal passed');
    }

    /**
     * Test URL with only protocol
     */
    #[Test]
    public function isInvalidUrlWithOnlyProtocol(): void
    {
        $url = 'https://';
        $this->assertFalse(Url::validate($url), 'URL with only protocol passed validation');
    }

    /**
     * Test empty URL
     */
    #[Test]
    public function isInvalidEmptyUrl(): void
    {
        $url = '';
        $this->assertFalse(Url::validate($url), 'Empty URL passed validation');
    }
}
