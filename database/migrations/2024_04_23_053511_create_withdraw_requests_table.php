<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_id')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->string('amount')->default('0.00');
            $table->string('transaction_note', 20)->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();
            $table->bigInteger('product_manager_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_requests');
    }
};