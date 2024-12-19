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
        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->nullable();
            $table->string('cart_group_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->string('color')->nullable();
            $table->text('choices')->nullable();
            $table->text('variations')->nullable();
            $table->text('variant')->nullable();
            $table->integer('quantity')->default(1);
            $table->double('price', 8, 2)->default(1);
            $table->double('tax', 8, 2)->default(1);
            $table->double('discount', 8, 2)->default(1);
            $table->string('slug')->nullable();
            $table->string('name')->nullable();
            $table->string('thumbnail')->nullable();
            $table->bigInteger('seller_id')->nullable();
            $table->string('seller_is')->default('admin');
            $table->timestamps();
            $table->string('shop_info')->nullable();
            $table->bigInteger('flash_deal_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
