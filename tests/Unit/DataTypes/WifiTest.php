<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\Wifi;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class WifiTest
 * Tests the Wifi class.
 */
class WifiTest extends TestCase
{
    /**
     * Provides a list of WiFi configuration strings and their expected validation results
     */
    public static function wifiStringProvider(): array
    {
        return [
            'Valid WPA2' => ['WIFI:T:WPA2;S:MyNetwork;P:password123;', true],
            'Valid WEP' => ['WIFI:T:WEP;S:MyNetwork;P:abcdef1234;', true],
            'Valid nopass' => ['WIFI:T:nopass;S:MyNetwork;P:;', true],
            'Valid WEP with 10-char password' => ['WIFI:T:WEP;S:MyNetwork;P:1234567890;', true],
            'Missing password for WPA2' => ['WIFI:T:WPA2;S:MyNetwork;', false],
            'Invalid SSID with semicolon' => ['WIFI:T:WPA2;S:My;Network;P:password123;', false],
            'Too short WPA2 password' => ['WIFI:T:WPA2;S:MyNetwork;P:short;', false],
            'Invalid encryption type' => ['WIFI:T:INVALID;S:MyNetwork;P:password123;', false],
            'Too short WEP password' => ['WIFI:T:WEP;S:MyNetwork;P:short;', false],
            'Too long WEP password' => ['WIFI:T:WEP;S:MyNetwork;P:1234567890123456789012345678901234;', false],
        ];
    }

    /**
     * Test Wi-Fi string validation using various valid and invalid cases
     */
    #[Test]
    #[DataProvider('wifiStringProvider')]
    public function wifiValidation(string $wifiString, bool $expected): void
    {
        $this->assertSame($expected, Wifi::validate($wifiString), 'Wi-Fi string failed validation: ' . $wifiString);
    }
}
