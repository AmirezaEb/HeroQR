<?php

namespace HeroQR\Tests\DataTypes;

use HeroQR\DataTypes\Url;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class UrlTest
 * Tests the Url class.
 */
class UrlTest extends TestCase
{
    /**
     * Test valid URL
     */
    #[Test]
    public function isValidUrl(): void
    {
        $url = 'https://www.example.com';
        $this->assertTrue(Url::validate($url), 'Valid URL failed validation');
    }

    /**
     * Test URL with incorrect structure
     */
    #[Test]
    public function isInvalidUrlWithIncorrectStructure(): void
    {
        $url = 'htp://example';
        $this->assertFalse(Url::validate($url), 'Invalid URL with incorrect structure passed');
    }

    /**
     * Test URL with invalid domain
     */
    #[Test]
    public function isInvalidUrlWithInvalidDomain(): void
    {
        $url = 'https://example.invalid';
        $this->assertFalse(Url::validate($url), 'Invalid URL with non-existent domain passed');
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
     * Test valid URL without security issues
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
     * Test URL with non-standard port
     */
    #[Test]
    public function isValidUrlWithNonStandardPort(): void
    {
        $url = 'http://example.com:8080';
        $this->assertTrue(Url::validate($url), 'Valid URL with non-standard port failed validation');
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
     * Test URL with a long query string
     */
    #[Test]
    public function isValidUrlWithLongQueryString(): void
    {
        $url = 'https://example.com/search?' . str_repeat('q=valid&', 100);
        $this->assertTrue(Url::validate($url), 'Valid URL with long query string failed validation');
    }
}
