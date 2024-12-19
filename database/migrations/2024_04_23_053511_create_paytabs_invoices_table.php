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
        Schema::create('paytabs_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->text('result');
            $table->unsignedInteger('response_code');
            $table->unsignedInteger('pt_invoice_id')->nullable();
            $table->double('amount', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->string('card_brand')->nullable();
            $table->unsignedInteger('card_first_six_digits')->nullable();
            $table->unsignedInteger('card_last_four_digits')->nullable();
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
        Schema::dropIfExists('paytabs_invoices');
    }
};
