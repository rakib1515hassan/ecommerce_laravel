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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('blog_category_id');
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->string('seo_title');
            $table->text('seo_description');
            $table->string('seo_keywords');
            $table->string('seo_image');
            $table->string('thumbnail');
            $table->bigInteger('created_by_id');
            $table->boolean('is_created_admin');
            $table->boolean('is_published');
            $table->boolean('is_approved');
            $table->boolean('is_featured');
            $table->boolean('is_commentable');
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
        Schema::dropIfExists('blog_posts');
    }
};
