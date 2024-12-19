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
        Schema::create('chattings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('seller_id');
            $table->text('message');
            $table->boolean('sent_by_customer')->default(false);
            $table->boolean('sent_by_seller')->default(false);
            $table->boolean('seen_by_customer')->default(true);
            $table->boolean('seen_by_seller')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->bigInteger('shop_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chattings');
    }
};
