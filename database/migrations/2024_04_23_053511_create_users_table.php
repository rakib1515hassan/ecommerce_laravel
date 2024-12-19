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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 80)->nullable();
            $table->string('f_name', 255)->nullable();
            $table->string('l_name', 255)->nullable();
            $table->string('phone', 25);
            $table->string('image', 30)->default('def.png');
            $table->string('email', 80)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 80);
            $table->rememberToken();
            $table->timestamps();
            $table->string('street_address', 250)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('house_no', 50)->nullable();
            $table->string('apartment_no', 50)->nullable();
            $table->string('cm_firebase_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('payment_card_last_four')->nullable();
            $table->string('payment_card_brand')->nullable();
            $table->text('payment_card_fawry_token')->nullable();
            $table->string('login_medium')->nullable();
            $table->string('social_id')->nullable();
            $table->boolean('is_phone_verified')->default(false);
            $table->string('temporary_token')->nullable();
            $table->boolean('is_email_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
