<?php

namespace HeroQR\Tests\DataTypes;

use HeroQR\DataTypes\Location;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class LocationTest
 * Tests the Location class.
 */
class LocationTest extends TestCase
{
    /**
     * Test for validating valid geographic coordinates
     */
    #[Test]
    public function isValidLocation(): void
    {
        $coordinates = [
            '51.3890,12.3',
            '51.3890,12.3,24',
            '-45.123,-123.456',
            '0,0',
            '90,180',
            '-90,-180',
        ];

        foreach ($coordinates as $coordinate) {
            $result = Location::validate($coordinate);
            $this->assertTrue($result, "Coordinates $coordinate should be valid");
        }
    }

    /**
     * Test for validating invalid geographic coordinates
     */
    #[Test]
    public function isInvalidLocation(): void
    {
        $coordinates = [
            '91,0',
            '0,181',
            '51.3890,12.3,abc',
            'abc,12.3',
            '51.3890,abc',
            '51.3890',
            '51.3890,12.3,24,5',
        ];

        foreach ($coordinates as $coordinate) {
            $result = Location::validate($coordinate);
            $this->assertFalse($result, "Coordinates $coordinate should be invalid");
        }
    }

    /**
     * Test for validating empty and malformed geographic coordinates
     */
    #[Test]
    public function isMalformedLocation(): void
    {
        $coordinates = [
            '',
            ' ',
            ',',
            '51.3890,',
            ',12.3',
            '51.3890,,12.3',
            '51.3890, 12.3, ',
        ];

        foreach ($coordinates as $coordinate) {
            $result = Location::validate($coordinate);
            $this->assertFalse($result, "Coordinates $coordinate should be invalid");
        }
    }
}
