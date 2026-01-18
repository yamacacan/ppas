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
        Schema::create('file_name', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->integer('order');
            $table->unsignedBigInteger('file_category_id');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('file_category_id')->references('id')->on('file_category')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_name');
    }
};
