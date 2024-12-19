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
        Schema::create('seller_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_id')->nullable();
            $table->double('total_earning', null, 0)->default(0);
            $table->double('withdrawn', null, 0)->default(0);
            $table->timestamps();
            $table->double('commission_given', 8, 2)->default(0);
            $table->double('pending_withdraw', 8, 2)->default(0);
            $table->double('delivery_charge_earned', 8, 2)->default(0);
            $table->double('collected_cash', 8, 2)->default(0);
            $table->double('total_tax_collected', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_wallets');
    }
};
