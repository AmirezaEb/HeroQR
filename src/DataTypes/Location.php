<?php

namespace HeroQR\DataTypes;

use HeroQR\Contracts\DataTypes\AbstractDataType;

/**
 * Class Location
 *
 * This class validates geographic coordinates (latitude, longitude, and optionally altitude).
 * The coordinates should be in the format "latitude,longitude,altitude" or "latitude,longitude".
 * The latitude must be between -90 and 90, and the longitude must be between -180 and 180.
 * If an altitude is provided, it must be a numeric value.
 *
 * Example: "51.3890, 12.3, 24" or "51.3890, 12.3"
 *
 * @package HeroQR\DataTypes
 */

class Location extends AbstractDataType
{
    public static function validate(string $coordinates): bool
    {
        $coordinates = trim($coordinates);
        
        $parts = explode(',', $coordinates);

        if (count($parts) < 2 || count($parts) > 3) {
            return false;
        }

        foreach ($parts as $part) {
            if (!is_numeric($part)) {
                return false;
            }
        }

        $latitude = (float)$parts[0];
        $longitude = (float)$parts[1];

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return false;
        }

        if (isset($parts[2])) {
            $altitude = (float)$parts[2];
            if (!is_numeric($altitude)) {
                return false;
            }
        }

        return true;
    }
}