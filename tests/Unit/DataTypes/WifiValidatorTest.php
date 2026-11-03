<?php

namespace HeroQR\Tests\Unit\DataTypes;

use HeroQR\DataTypes\WifiValidator;
use PHPUnit\Framework\{Attributes\DataProvider, Attributes\Test, TestCase};

/**
 * Class WifiTest
 * Tests the Wifi class.
 */
class WifiValidatorTest extends TestCase
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
            'Valid WPA' => ['WIFI:T:WPA;S:OfficeNet;P:officepass;', true],
            'Valid WPA/WPA2-Personal' => ['WIFI:T:WPA/WPA2-Personal;S:HomeNet;P:homepass1;', true],
            'Valid WEP with 26-char password' => ['WIFI:T:WEP;S:MyNetwork;P:0123456789abcdef0123456789;', true],
            'Valid max length SSID' => ['WIFI:T:WPA2;S:' . str_repeat('A', 32) . ';P:password123;', true],
            'Invalid SSID with colon' => ['WIFI:T:WPA2;S:My:Network;P:password123;', false],
            'Invalid SSID too long' => ['WIFI:T:WPA2;S:' . str_repeat('A', 33) . ';P:password123;', false],
            'Invalid WPA password too long' => ['WIFI:T:WPA;S:MyNetwork;P:' . str_repeat('p', 64) . ';', false],
            'Invalid nopass with password' => ['WIFI:T:nopass;S:MyNetwork;P:shouldBeEmpty;', false],
            'Invalid empty string' => ['', false],
            'Invalid missing WIFI prefix' => ['T:WPA2;S:MyNetwork;P:password123;', false],
            'Invalid WEP non-hex password' => ['WIFI:T:WEP;S:MyNetwork;P:ghijklmnop;', false],
        ];
    }


    /**
     * Tests Wi-Fi string validation using various valid and invalid cases
     */
    #[Test]
    #[DataProvider('wifiStringProvider')]
    public function isWifiValid(string $wifiString, bool $expected): void
    {
        $this->assertSame($expected, WifiValidator::validate($wifiString), 'Wi-Fi string failed validation: ' . $wifiString);
    }
}
