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
        Schema::table('address_areas', function (Blueprint $table) {
            $table->foreign(['district_id'])->references(['id'])->on('address_districts')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['division_id'])->references(['id'])->on('address_divisions')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['zone_id'])->references(['id'])->on('address_zones')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('address_areas', function (Blueprint $table) {
            $table->dropForeign('address_areas_district_id_foreign');
            $table->dropForeign('address_areas_division_id_foreign');
            $table->dropForeign('address_areas_zone_id_foreign');
        });
    }
};
