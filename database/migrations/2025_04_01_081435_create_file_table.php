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
        Schema::create('file', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->unsignedBigInteger('file_name_id');
            $table->enum('status',['publish','revision']);
            $table->timestamp('uploaded_date');
            $table->unsignedBigInteger('uploaded_user');
            $table->timestamp('revision_date')->nullable();
            $table->integer('revision_order')->nullable();
            $table->text('revision_explain')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('file_name_id')->references('id')->on('file_name')->noActionOnDelete();
            $table->foreign('uploaded_user')->references('id')->on('users')->noActionOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file');
    }
};
