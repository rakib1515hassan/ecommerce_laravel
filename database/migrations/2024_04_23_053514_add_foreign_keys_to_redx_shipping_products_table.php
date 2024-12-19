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
        Schema::table('redx_shipping_products', function (Blueprint $table) {
            $table->foreign(['redx_profile_id'])->references(['id'])->on('redx_profiles')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('redx_shipping_products', function (Blueprint $table) {
            $table->dropForeign('redx_shipping_products_redx_profile_id_foreign');
        });
    }
};
