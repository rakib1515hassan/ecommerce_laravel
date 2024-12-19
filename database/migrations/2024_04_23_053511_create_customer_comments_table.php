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
        Schema::create('customer_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('comment');
            $table->boolean('is_reply')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('created_by_id');
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
        Schema::dropIfExists('customer_comments');
    }
};
