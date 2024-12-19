<?php

namespace App\Library\Redx;

use Illuminate\Support\Facades\Cache;

class RedxArea
{
    public function getAreas()
    {
        if (Cache::has('redx_areas')) {
            return Cache::get('redx_areas');
        }

        $api = new RedxAPI();
        $areas = $api->getAreas();


        // cache the areas for 24 hours
        Cache::add('redx_areas', $areas['areas'], 60 * 24);
        return $areas['areas'];
    }

    public function getDistricts()
    {
        if (Cache::has('redx_districts')) {
            return Cache::get('redx_districts');
        }
        $areas = $this->getAreas();
        $districts = [];
        foreach ($areas as $area) {
            $districts[] = $area['district_name'];
        }

        $districts = array_unique($districts);
        // sort  by alphabetical order
        sort($districts);
        Cache::add('redx_districts', array_unique($districts), 60 * 24);
        return $districts;
    }

    public function getAreasByDistrict($district)
    {
        $areas = $this->getAreas();
        $district_areas = [];
        foreach ($areas as $area) {
            if ($area['district_name'] == $district) {
                $district_areas[] = $area;
            }
        }
        return $district_areas;
    }
}
