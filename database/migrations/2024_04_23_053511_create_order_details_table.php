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
        Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->bigInteger('seller_id')->nullable();
            $table->text('product_details')->nullable();
            $table->integer('qty')->default(0);
            $table->double('price', null, 0)->default(0);
            $table->double('tax', null, 0)->default(0);
            $table->double('discount', null, 0)->default(0);
            $table->string('delivery_status', 15)->default('pending');
            $table->string('payment_status', 15)->default('unpaid');
            $table->timestamps();
            $table->bigInteger('shipping_method_id')->nullable();
            $table->string('variant', 255)->nullable();
            $table->string('variation', 255)->nullable();
            $table->string('discount_type', 30)->nullable();
            $table->boolean('is_stock_decreased')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
};
