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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->tinyInteger('level')->default(1)->comment('1: Ana kategori, 2: Alt kategori, 3: Alt-alt kategori');
            $table->enum('type', ['work', 'other'])->default('work')->comment('İş veya Diğer');
            $table->string('color', 7)->nullable()->comment('Hex renk kodu (#FF5733)');
            $table->string('icon', 100)->nullable()->comment('Icon class veya path');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            
            // Indexes
            $table->index('parent_id', 'idx_parent_id');
            $table->index('type', 'idx_type');
            $table->index('level', 'idx_level');
            $table->index('slug', 'idx_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
