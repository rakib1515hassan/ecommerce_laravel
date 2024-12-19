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
        Schema::create('product_managers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('seller_id', 255)->nullable();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->nullable();
            $table->string('identity_number', 30)->nullable();
            $table->string('identity_type', 50)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('password', 100);
            $table->boolean('is_active')->default(false);
            $table->string('auth_token')->default('cW0Kk97Cum8H1DoaN7RFFhM293232sCCMiuRXWGE');
            $table->string('fcm_token')->nullable();
            $table->timestamps();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_no')->nullable();
            $table->string('holder_name')->nullable();
            $table->double('sales_commission_percentage', null, 0)->nullable();
            $table->string('gst')->nullable();
            $table->string('cm_firebase_token')->nullable();
            $table->bigInteger('balance')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_managers');
    }
};
