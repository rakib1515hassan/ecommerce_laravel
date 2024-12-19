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
        Schema::create('delivery_men', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_id')->nullable();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->nullable();
            $table->string('identity_number', 30)->nullable();
            $table->string('identity_type', 50)->nullable();
            $table->string('identity_image')->nullable();
            $table->string('image', 100)->nullable();
            $table->string('password', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->string('auth_token')->default('6yIRXJRRfp78qJsAoKZZ6TTqhzuNJ3TcdvPBmk6n');
            $table->string('fcm_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_men');
    }
};
