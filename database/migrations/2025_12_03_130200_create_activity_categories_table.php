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
        Schema::create('activity_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('category_id');
            $table->string('matched_keyword', 255)->nullable();
            $table->string('match_type', 50)->nullable();
            $table->decimal('confidence_score', 5, 2)->default(100.00);
            $table->boolean('is_manual')->default(false);
            $table->timestamp('tagged_at')->useCurrent();
            
            // Foreign key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Indexes
            $table->index('activity_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_categories');
    }
};
