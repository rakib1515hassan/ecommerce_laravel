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
        Schema::create('admin_wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('admin_id')->nullable();
            $table->double('inhouse_earning', null, 0)->default(0);
            $table->double('withdrawn', null, 0)->default(0);
            $table->timestamps();
            $table->double('commission_earned', 8, 2)->default(0);
            $table->double('delivery_charge_earned', 8, 2)->default(0);
            $table->double('pending_amount', 8, 2)->default(0);
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
        Schema::dropIfExists('admin_wallets');
    }
};
