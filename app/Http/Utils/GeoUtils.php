<?php

namespace App\Http\Utils;

class GeoUtils
{
    /**
     * Calculate the shortest distance between two points.
     *
     * Returns KM.
     */
    public static function distance($latitude1, $longitude1, $latitude2, $longitude2): float
    {
        return self::haversine($latitude1, $longitude1, $latitude2, $longitude2);
    }

    /**
     * Calculates the shortest distance between two points
     * while assuming that the earth is perfectly round.
     */
    private static function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        // Earth radius
        $R = 6371.0088;

        // Convert degrees to radians
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $dPhi = deg2rad($lat2 - $lat1);
        $dLambda = deg2rad($lon2 - $lon1);

        // Haversine formula
        $a = sin($dPhi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($dLambda / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c; // distance in km
    }
}
