<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_areas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('division_id')->index('address_areas_division_id_foreign');
            $table->unsignedBigInteger('district_id')->index('address_areas_district_id_foreign');
            $table->unsignedBigInteger('zone_id')->index('address_areas_zone_id_foreign');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address_areas');
    }
};
