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
        Schema::create('video_post_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('video_post_id')->index('video_post_comments_post_id_foreign');
            $table->unsignedBigInteger('user_id')->index('video_post_comments_user_id_foreign');
            $table->text('comment');
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
        Schema::dropIfExists('video_post_comments');
    }
};
