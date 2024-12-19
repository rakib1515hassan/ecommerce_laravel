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
        Schema::table('redx_profiles', function (Blueprint $table) {
            $table->foreign(['seller_id'])->references(['id'])->on('sellers')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redx_profiles', function (Blueprint $table) {
            $table->dropForeign('redx_profiles_seller_id_foreign');
        });
    }
};
