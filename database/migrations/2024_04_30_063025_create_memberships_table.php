<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('referred_from')->nullable();

            $table->string('referral_id', 10)->unique();
            $table->integer('points')->default(0);
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');

            $table->enum('verification_type', ['nid', 'passport'])->default('nid');
            $table->string('verification_id', 20)->unique();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_from')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
