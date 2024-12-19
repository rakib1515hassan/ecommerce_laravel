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
        Schema::table('post_reactions', function (Blueprint $table) {
            $table->foreign(['post_id'])->references(['id'])->on('posts')->onUpdate('no action')->onDelete('cascade');
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
        Schema::table('post_reactions', function (Blueprint $table) {
            $table->dropForeign('post_reactions_post_id_foreign');
            $table->dropForeign('post_reactions_user_id_foreign');
        });
    }
};
