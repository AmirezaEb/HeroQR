<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Wifi
 *
 * This class validates Wi-Fi credentials for QR Code generation. It ensures the Wi-Fi credentials 
 * follow the correct format based on the specified encryption type and SSID/password criteria.
 * 
 * The valid format for Wi-Fi credentials is:
 *   WIFI:T:<encryptionType>;S:<SSID>;P:<password>;
 * 
 * Supported encryption types:
 * - WPA
 * - WPA2
 * - WEP
 * - nopass (no password required)
 * - WPA/WPA2-Personal (treated as WPA2)
 * 
 * The class performs the following checks:
 * - Ensures the SSID is valid (max length of 32 characters, no special characters like ';' or ':')
 * - Validates the password based on the encryption type:
 *   - For WPA and WPA2: password length should be between 8 and 63 characters.
 *   - For WEP: password length should be either 10 or 26 characters and must be hexadecimal.
 *   - For nopass: no password should be provided.
 *
 * Example usage:
 *   WIFI:T:WPA2;S:MyNetwork;P:password123;
 * 
 * @package HeroQR\DataTypes
 */

class Wifi extends AbstractDataType
{
    public static function validate(string $wifiString): bool
    {
        $pattern = '/^WIFI:T:(WPA|WPA2|WEP|nopass|WPA\/WPA2-Personal);S:([^;]+);(?:P:([^;]*))?;$/';

        if (empty($wifiString) || !preg_match($pattern, $wifiString, $matches)) {
            return false;
        }

        list(, $encryptionType, $ssid, $password) = $matches;

        if ($encryptionType === 'WPA/WPA2-Personal') {
            $encryptionType = 'WPA2';
        }

        if (strlen($ssid) > 32 || preg_match('/[;:]/', $ssid)) {
            return false;
        }

        if (in_array($encryptionType, ['WPA', 'WPA2'])) {
            if (empty($password) || strlen($password) < 8 || strlen($password) > 63) {
                return false;
            }
        }

        if ($encryptionType === 'WEP') {
            if (empty($password) || !in_array(strlen($password), [10, 26]) || !ctype_xdigit($password)) {
                return false;
            }
        }

        if ($encryptionType === 'nopass') {
            if (!empty($password)) {
                return false;
            }
        }

        return true;
    }
}
