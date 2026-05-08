<?php

namespace App\Services;

use App\Models\City;

class LocationService
{
    public static function fetchLocation()
    {
        // $city = City::find(145);
        // return ['country_id' => '150', 'country_code' => 'AE', 'currency_code' => 'AED', 'currency_symbol' => 'د.إ', 'city_name' => $city->name ?? 'rajkot', 'city_id' => $city->id ?? 4,];

        $city = City::find(27);
        return ['country_id' => '70', 'country_code' => 'IN', 'currency_code' => 'INR', 'currency_symbol' => '₹', 'city_name' => $city->name ?? 'rajkot', 'city_id' => $city->id ?? 4,];
    }
}
