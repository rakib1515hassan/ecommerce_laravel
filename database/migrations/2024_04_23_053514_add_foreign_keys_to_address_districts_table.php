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
        Schema::table('address_districts', function (Blueprint $table) {
            $table->foreign(['division_id'])->references(['id'])->on('address_divisions')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('address_districts', function (Blueprint $table) {
            $table->dropForeign('address_districts_division_id_foreign');
        });
    }
};
