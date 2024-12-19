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
        Schema::create('reseller_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('reseller_id')->nullable();
            $table->double('total_earning', 8, 2)->default(0);
            $table->double('pending_withdraw', 8, 2)->default(0);
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
        Schema::dropIfExists('reseller_wallets');
    }
};
