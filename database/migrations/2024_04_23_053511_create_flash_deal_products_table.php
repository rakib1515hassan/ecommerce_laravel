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
        Schema::create('flash_deal_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('flash_deal_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->decimal('discount')->default(0);
            $table->string('discount_type', 20)->nullable();
            $table->timestamps();
            $table->string('seller_is', 20)->nullable();
            $table->boolean('status')->nullable()->default(false);
            $table->bigInteger('seller_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flash_deal_products');
    }
};
