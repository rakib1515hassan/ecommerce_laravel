<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('point_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable();
            $table->float('order_amount', 8, 2)->nullable();
            $table->enum('status', ['self', 'referred'])->default('self');

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_histories', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
            $table->dropColumn('order_amount');
        });
    }
};
