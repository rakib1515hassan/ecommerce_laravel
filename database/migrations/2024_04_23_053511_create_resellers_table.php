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
        Schema::create('resellers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('identity_number', 30)->nullable();
            $table->string('identity_type', 50)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('password', 100);
            $table->boolean('is_active')->default(false);
            $table->string('auth_token')->default('6VpdMe1I6YdWDyIWkAiGgXr8Umg5CGyKplAAJBCn');
            $table->string('fcm_token')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_no')->nullable();
            $table->string('holder_name')->nullable();
            $table->double('sales_commission_percentage', null, 0)->nullable();
            $table->string('gst')->nullable();
            $table->string('cm_firebase_token')->nullable();
            $table->float('balance', null, 0)->default(0);
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
        Schema::dropIfExists('resellers');
    }
};
