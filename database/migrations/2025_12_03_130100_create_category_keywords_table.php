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
        Schema::create('category_keywords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('keyword', 255)->comment('Aranacak kelime veya ifade');
            $table->enum('match_type', ['exact', 'contains', 'starts_with', 'ends_with', 'regex'])->default('contains');
            $table->boolean('is_case_sensitive')->default(false);
            $table->integer('priority')->default(0)->comment('Yüksek öncelik önce kontrol edilir');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Indexes
            $table->index('category_id', 'idx_category_id');
            $table->index('keyword', 'idx_keyword');
            $table->index('priority', 'idx_priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_keywords');
    }
};
