<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Location;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class LocationTest
 * Tests the Location class
 */
class LocationTest extends TestCase
{
    /*
     * Provides various coordinate strings and expected validation results
     */
    public static function coordinatesProvider(): array
    {
        return [
            # Valid coordinates
            'Valid simple' => ['51.3890,12.3', true],
            'Valid with altitude' => ['51.3890,12.3,24', true],
            'Valid negative' => ['-45.123,-123.456', true],
            'Valid zero' => ['0,0', true],
            'Valid max' => ['90,180', true],
            'Valid min' => ['-90,-180', true],

            # Invalid coordinates
            'Invalid latitude high' => ['91,0', false],
            'Invalid longitude high' => ['0,181', false],
            'Invalid altitude text' => ['51.3890,12.3,abc', false],
            'Invalid latitude text' => ['abc,12.3', false],
            'Invalid longitude text' => ['51.3890,abc', false],
            'Missing longitude' => ['51.3890', false],
            'Too many parts' => ['51.3890,12.3,24,5', false],

            # Malformed / empty
            'Empty string' => ['', false],
            'Only whitespace' => [' ', false],
            'Only comma' => [',', false],
            'Missing longitude value' => ['51.3890,', false],
            'Missing latitude value' => [',12.3', false],
            'Double comma' => ['51.3890,,12.3', false],
            'Trailing comma' => ['51.3890, 12.3, ', false],
        ];
    }

    /**
     * Tests location validation using various coordinates
     */
    #[Test]
    #[DataProvider('coordinatesProvider')]
    public function testLocationValidation(string $coordinate, bool $expected): void
    {
        $result = Location::validate($coordinate);
        $this->assertSame($expected, $result, "Coordinate validation failed for: \"$coordinate\"");
    }
}
