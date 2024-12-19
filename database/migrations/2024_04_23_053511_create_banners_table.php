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
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('photo', 255)->nullable();
            $table->string('banner_type', 255);
            $table->integer('published')->default(0);
            $table->timestamps();
            $table->string('url', 255)->nullable();
            $table->string('resource_type')->nullable();
            $table->bigInteger('resource_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banners');
    }
};
