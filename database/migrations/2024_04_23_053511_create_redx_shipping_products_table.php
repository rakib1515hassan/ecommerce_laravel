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
        Schema::create('redx_shipping_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('redx_profile_id')->index('redx_shipping_products_redx_profile_id_foreign');
            $table->string('tracking_id', 200);
            $table->unsignedBigInteger('order_id');
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
        Schema::dropIfExists('redx_shipping_products');
    }
};
