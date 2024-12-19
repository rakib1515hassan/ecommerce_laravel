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
        Schema::create('deal_of_the_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150)->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->decimal('discount')->default(0);
            $table->string('discount_type', 12)->default('amount');
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('deal_of_the_days');
    }
};
