<?php

namespace HeroQR\Tests\Unit\Customs;

use HeroQR\Customs\{CursorPaths, MarkerPaths, ShapePaths};
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class CustomPathsApiTest
 * Tests AbstractCustomPaths API methods: isValidKey, getPath and getValueByKey.
 */
class CustomPathsApiTest extends TestCase
{
    /**
     * Provides valid keys for each custom path class
     */
    public static function validKeyProvider(): array
    {
        return [
            'Cursor C1' => [CursorPaths::class, 'C1'],
            'Cursor C7' => [CursorPaths::class, 'C7'],
            'Marker M1' => [MarkerPaths::class, 'M1'],
            'Marker M7' => [MarkerPaths::class, 'M7'],
            'Shape S1' => [ShapePaths::class, 'S1'],
            'Shape S4' => [ShapePaths::class, 'S4'],
        ];
    }

    /**
     * Test isValidKey returns true for known keys
     */
    #[Test]
    #[DataProvider('validKeyProvider')]
    public function isValidKeyReturnsTrue(string $class, string $key): void
    {
        $this->assertTrue($class::isValidKey($key));
    }

    /**
     * Provides invalid keys for each custom path class
     */
    public static function invalidKeyProvider(): array
    {
        return [
            'Cursor invalid' => [CursorPaths::class, 'C99'],
            'Marker invalid' => [MarkerPaths::class, 'MX'],
            'Shape invalid' => [ShapePaths::class, 'S9'],
            'Empty key' => [ShapePaths::class, ''],
        ];
    }

    /**
     * Test isValidKey returns false for unknown keys
     */
    #[Test]
    #[DataProvider('invalidKeyProvider')]
    public function isValidKeyReturnsFalse(string $class, string $key): void
    {
        $this->assertFalse($class::isValidKey($key));
    }

    /**
     * Test getPath returns the expected path/method for valid keys
     */
    #[Test]
    #[DataProvider('validKeyProvider')]
    public function isGetPathReturnsValue(string $class, string $key): void
    {
        $path = $class::getPath($key);
        $this->assertIsString($path);
        $this->assertNotEmpty($path);
        $this->assertSame($class::getAllPaths()[$key], $path);
    }

    /**
     * Test getPath throws for invalid keys
     */
    #[Test]
    public function isGetPathInvalidKeyThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid marker key');

        MarkerPaths::getPath('M99');
    }

    /**
     * Test getValueByKey returns values for valid keys
     */
    #[Test]
    #[DataProvider('validKeyProvider')]
    public function isGetValueByKeyReturnsValue(string $class, string $key): void
    {
        $value = $class::getValueByKey($key);
        $this->assertSame($class::getAllPaths()[$key], $value);
    }

    /**
     * Test getValueByKey throws a detailed exception for invalid keys
     */
    #[Test]
    public function isGetValueByKeyInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches("/Invalid key 'C99' provided/");

        CursorPaths::getValueByKey('C99');
    }

    /**
     * Test ShapePaths keys map to draw method names
     */
    #[Test]
    public function isShapePathKeysMapToDrawMethods(): void
    {
        $this->assertSame('drawSquare', ShapePaths::getValueByKey('S1'));
        $this->assertSame('drawCircle', ShapePaths::getValueByKey('S2'));
        $this->assertSame('drawStar', ShapePaths::getValueByKey('S3'));
        $this->assertSame('drawDiamond', ShapePaths::getValueByKey('S4'));
    }

    /**
     * Test CursorPaths and MarkerPaths values end with .png and exist
     */
    #[Test]
    public function isAssetPathsAreReadablePngFiles(): void
    {
        foreach (array_merge(CursorPaths::getAllPaths(), MarkerPaths::getAllPaths()) as $path) {
            $this->assertFileExists($path);
            $this->assertFileIsReadable($path);
            $this->assertStringEndsWith('.png', $path);
        }
    }

    /**
     * Test getAllPaths returns associative arrays with expected key prefixes
     */
    #[Test]
    public function isGetAllPathsKeyPrefixes(): void
    {
        foreach (array_keys(CursorPaths::getAllPaths()) as $key) {
            $this->assertMatchesRegularExpression('/^C\d+$/', $key);
        }

        foreach (array_keys(MarkerPaths::getAllPaths()) as $key) {
            $this->assertMatchesRegularExpression('/^M\d+$/', $key);
        }

        foreach (array_keys(ShapePaths::getAllPaths()) as $key) {
            $this->assertMatchesRegularExpression('/^S\d+$/', $key);
        }
    }
}
