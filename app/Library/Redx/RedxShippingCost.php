<?php

namespace App\Library\Redx;

class RedxShippingCost
{
    // 1 = "outside Dhaka"
    // 2 = "inside Dhaka"
    // 3 = "subrub Dhaka"
    private static array $shipping_cost = [
        [
            'source' => 1,
            'destination' => 1,
            'cost' => 130.00,
            'per_kg' => 30.00
        ],
        [
            'source' => 1,
            'destination' => 2,
            'cost' => 130.00,
            'per_kg' => 30.00
        ],
        [
            'source' => 1,
            'destination' => 3,
            'cost' => 130.00,
            'per_kg' => 30.00
        ],
        [
            'source' => 2,
            'destination' => 2,
            'cost' => 60.00,
            'per_kg' => 15.00
        ],
        [
            'source' => 2,
            'destination' => 3,
            'cost' => 100.00,
            'per_kg' => 15.00
        ],
        [
            'source' => 3,
            'destination' => 3,
            'cost' => 60.00,
            'per_kg' => 15.00
        ]
    ];

    /**
     * @param $source_zone
     * @param $destination_zone
     * @param int $weight in grams
     * @return float|int
     */
    public static function getShippingCost($source_zone, $destination_zone, int $weight = 0): float|int
    {
        $source_zone = $source_zone == 7 ? 3 : $source_zone;
        $destination_zone = $destination_zone == 7 ? 3 : $destination_zone;

        $shipping_cost = array_filter(self::$shipping_cost, function ($item) use ($source_zone, $destination_zone) {
            return ($item['source'] == $source_zone && $item['destination'] == $destination_zone) ||
                ($item['source'] == $destination_zone && $item['destination'] == $source_zone);
        });

        $cost = $shipping_cost[array_key_first($shipping_cost)];

        $weight = $weight / 1000;
        $cost['extra'] = ceil($weight - 1) * $cost['per_kg'];

        return $cost['cost'] + $cost['extra'];
    }
}
