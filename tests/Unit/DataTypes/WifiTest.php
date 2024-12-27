<?php

namespace HeroQR\Tests\DataTypes;

use HeroQR\DataTypes\Wifi;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class WifiTest
 * Tests the Wifi class.
 */
class WifiTest extends TestCase
{
    /**
     * Test valid WPA2 Wi-Fi string with password
     */
    #[Test]
    public function isValidWifiWpa2(): void
    {
        $wifiString = 'WIFI:T:WPA2;S:MyNetwork;P:password123;';
        $this->assertTrue(Wifi::validate($wifiString), 'Valid WPA2 Wi-Fi string with password should pass');
    }

    /**
     * Test valid WEP Wi-Fi string with password
     */
    #[Test]
    public function isValidWifiWep(): void
    {
        $wifiString = 'WIFI:T:WEP;S:MyNetwork;P:abcdef1234;';
        $this->assertTrue(Wifi::validate($wifiString), 'Valid WEP Wi-Fi string with password should pass');
    }

    /**
     * Test valid nopass Wi-Fi string (no password)
     */
    #[Test]
    public function isValidWifiNopass(): void
    {
        $wifiString = 'WIFI:T:nopass;S:MyNetwork;P:;';
        $this->assertTrue(Wifi::validate($wifiString), 'Valid nopass Wi-Fi string should pass');
    }

    /**
     * Test invalid WPA2 Wi-Fi string with missing password
     */
    #[Test]
    public function isInvalidWifiMissingPasswordForWpa2(): void
    {
        $wifiString = 'WIFI:T:WPA2;S:MyNetwork;';
        $this->assertFalse(Wifi::validate($wifiString), 'WPA2 Wi-Fi string with missing password should fail');
    }

    /**
     * Test invalid Wi-Fi string with invalid SSID (contains a semicolon)
     */
    #[Test]
    public function isInvalidWifiWithInvalidSsid(): void
    {
        $wifiString = 'WIFI:T:WPA2;S:My;Network;P:password123;';
        $this->assertFalse(Wifi::validate($wifiString), 'Wi-Fi string with invalid SSID should fail');
    }

    /**
     * Test invalid WPA2 Wi-Fi string with password that is too short
     */
    #[Test]
    public function isInvalidWifiWithToShortPasswordForWpa2(): void
    {
        $wifiString = 'WIFI:T:WPA2;S:MyNetwork;P:short;';
        $this->assertFalse(Wifi::validate($wifiString), 'WPA2 Wi-Fi string with a too short password should fail');
    }

    /**
     * Test invalid Wi-Fi string with an unsupported encryption type
     */
    #[Test]
    public function isInvalidWifiWithInvalidEncryptionType(): void
    {
        $wifiString = 'WIFI:T:INVALID;S:MyNetwork;P:password123;';
        $this->assertFalse(Wifi::validate($wifiString), 'Wi-Fi string with unsupported encryption type should fail');
    }

    /**
     * Test invalid WEP Wi-Fi string with password length that is too short or too long
     */
    #[Test]
    public function isInvalidWifiWithInvalidPasswordLengthForWep(): void
    {
        $wifiString = 'WIFI:T:WEP;S:MyNetwork;P:short;';
        $this->assertFalse(Wifi::validate($wifiString), 'WEP Wi-Fi string with a too short password should fail');

        $wifiString = 'WIFI:T:WEP;S:MyNetwork;P:1234567890123456789012345678901234;';
        $this->assertFalse(Wifi::validate($wifiString), 'WEP Wi-Fi string with a too long password should fail');
    }

    /**
     * Test valid WEP Wi-Fi string with a valid password length
     */
    #[Test]
    public function isValidWifiWithNoPasswordForWep(): void
    {
        $wifiString = 'WIFI:T:WEP;S:MyNetwork;P:1234567890;';
        $this->assertTrue(Wifi::validate($wifiString), 'WEP Wi-Fi string with valid password length should pass');
    }
}
