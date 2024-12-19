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
        Schema::create('video_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 2000);
            $table->string('slug', 2000)->nullable();
            $table->string('video', 2000)->nullable();
            $table->text('description');
            $table->unsignedBigInteger('created_by_id')->index('video_posts_created_by_id_foreign');
            $table->integer('is_approved')->default(0);
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
        Schema::dropIfExists('video_posts');
    }
};
