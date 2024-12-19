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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_id', 15)->nullable();
            $table->string('customer_type', 10)->nullable();
            $table->string('payment_status', 15)->default('unpaid');
            $table->string('order_status', 50)->default('pending');
            $table->string('payment_method', 100)->nullable();
            $table->string('transaction_ref', 30)->nullable();
            $table->double('order_amount', null, 0)->default(0);
            $table->bigInteger('shipping_address_id');
            $table->timestamps();
            $table->double('discount_amount', null, 0)->default(0);
            $table->string('discount_type', 30)->nullable();
            $table->string('coupon_code')->nullable();
            $table->bigInteger('shipping_method_id')->default(0);
            $table->double('shipping_cost', 8, 2)->default(0);
            $table->boolean('is_shipped')->default(false);
            $table->string('order_group_id')->default('def-order-group');
            $table->string('verification_code')->default('0');
            $table->bigInteger('seller_id')->nullable();
            $table->string('seller_is')->nullable();
            $table->bigInteger('reseller_id')->nullable();
            $table->text('shipping_address_data')->nullable();
            $table->bigInteger('delivery_man_id')->nullable();
            $table->text('order_note')->nullable();
            $table->unsignedBigInteger('billing_address')->nullable();
            $table->text('billing_address_data')->nullable();
            $table->integer('is_delivered')->default(0);
            $table->integer('is_paid')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
