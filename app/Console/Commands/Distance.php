<?php 

namespace App\Console\Commands;

class Distance {
    public static function Distance(float $lat1, float $lon1, float $lat2, float $lon2): float
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

        return $R * $c; // distance in chosen unit
    }
}