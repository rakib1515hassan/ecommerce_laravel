<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropColumn('product_manager_id');
            $table->dropColumn('seller_id');
            $table->enum('person', ['seller', 'reseller', 'product_manager'])->default('seller');
            $table->bigInteger('person_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->bigInteger('product_manager_id')->nullable();
            $table->bigInteger('seller_id')->nullable();
            $table->dropColumn('person');
            $table->dropColumn('person_id');
        });
    }
}
