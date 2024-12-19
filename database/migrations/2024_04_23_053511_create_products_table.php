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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('added_by')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('name', 80)->nullable();
            $table->string('slug', 120)->nullable();
            $table->string('category_ids', 80)->nullable();
            $table->bigInteger('brand_id')->nullable();
            $table->string('unit')->nullable();
            $table->integer('min_qty')->default(1);
            $table->boolean('refundable')->default(true);
            $table->string('images', 255)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->string('featured', 255)->nullable();
            $table->string('flash_deal', 255)->nullable();
            $table->string('video_provider', 30)->nullable();
            $table->string('video_url', 150)->nullable();
            $table->string('colors', 150)->nullable();
            $table->boolean('variant_product')->default(false);
            $table->string('attributes', 255)->nullable();
            $table->text('choice_options')->nullable();
            $table->text('variation')->nullable();
            $table->boolean('published')->default(false);
            $table->double('unit_price', null, 0)->default(0);
            $table->double('purchase_price', null, 0)->default(0);
            $table->string('tax')->default('0.00');
            $table->string('tax_type', 80)->nullable();
            $table->float('discount', null, 0)->default(0);
            $table->string('discount_type', 80)->nullable();
            $table->integer('current_stock')->nullable();
            $table->text('details')->nullable();
            $table->boolean('free_shipping')->default(false);
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->boolean('status')->default(true);
            $table->boolean('featured_status')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_image')->nullable();
            $table->boolean('request_status')->default(false);
            $table->string('denied_note')->nullable();
            $table->integer('weight')->nullable()->default(1000);
            $table->string('policy', 500)->nullable();
            $table->bigInteger('product_manager_id')->nullable();
            $table->integer('product_manager_amount')->default(0);
            $table->tinyInteger('is_admin_manage')->default(0);
            $table->float('seller_amount', null, 0)->default(0);
            $table->float('reseller_amount', null, 0)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
