<?php

namespace App\Console\Commands;

use App\Library\Redx\RedxArea;
use App\Models\AddressArea;
use App\Models\AddressDistrict;
use App\Models\AddressDivision;
use Illuminate\Console\Command;

class RedxAddressSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:redx-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // clean all three table AddressArea, AddressDistrict, AddressDivision
//        AddressArea::truncate();
//        AddressDistrict::truncate();
//        AddressDivision::truncate();

        // get all areas from redx api
        $redxArea = new RedxArea();
        $areas = $redxArea->getAreas();
        // get all districts from redx api

        for ($i = 0; $i < count($areas); $i++) {
            $district_name = $areas[$i]['district_name'];
            $division_name = $areas[$i]['division_name'];
            $area = $areas[$i]['name'];
            $id = $areas[$i]['id'];
            $zone = $areas[$i]['zone_id'];

//            dd($district, $division, $area, $id, $zone);

            // insert division
            $division = AddressDivision::where('name', $division_name)->first();
            if (!$division) {
                $division = AddressDivision::create([
                    'id' => AddressDivision::count() + 1,
                    'name' => $division_name
                ]);
            }

            // insert district
            $district = AddressDistrict::where('name', $district_name)->first();
            if (!$district) {
                $district = AddressDistrict::create([
                    'id' => AddressDistrict::count() + 1,
                    'name' => $district_name,
                    'division_id' => $division->id
                ]);
            }

            // insert area
            AddressArea::create([
                'id' => $id,
                'name' => $area,
                'district_id' => $district->id,
                'division_id' => $division->id,
                'zone_id' => $zone
            ]);
        }
    }
}
