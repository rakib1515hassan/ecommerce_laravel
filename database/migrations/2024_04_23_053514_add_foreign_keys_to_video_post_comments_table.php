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
        Schema::table('video_post_comments', function (Blueprint $table) {
            $table->foreign(['video_post_id'], 'video_post_comments_post_id_foreign')->references(['id'])->on('video_posts')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_post_comments', function (Blueprint $table) {
            $table->dropForeign('video_post_comments_post_id_foreign');
            $table->dropForeign('video_post_comments_user_id_foreign');
        });
    }
};
