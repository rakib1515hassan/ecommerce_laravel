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
        Schema::create('sellers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('f_name', 30)->nullable();
            $table->string('l_name', 30)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('image', 30)->default('def.png');
            $table->string('email', 80)->unique();
            $table->string('password', 80)->nullable();
            $table->string('status', 15)->default('pending');
            $table->rememberToken();
            $table->timestamps();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_no')->nullable();
            $table->string('holder_name')->nullable();
            $table->text('auth_token')->nullable();
            $table->double('sales_commission_percentage', 8, 2)->nullable();
            $table->string('gst')->nullable();
            $table->string('cm_firebase_token')->nullable();
            $table->tinyInteger('admin_manage')->default(0);
            $table->float('balance', null, 0)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sellers');
    }
};
