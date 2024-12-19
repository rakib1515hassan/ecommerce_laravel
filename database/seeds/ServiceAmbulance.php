<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceAmbulance extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $query = ServiceCategory::where('category_type', 'ambulance')->first();

        if (is_null($query)) {

            $ambulance = new ServiceCategory();
            $ambulance->name = "ambulance service";
            $ambulance->logo = "";
            $ambulance->category_type = "ambulance";
            $ambulance->save();

        };

    }
}
