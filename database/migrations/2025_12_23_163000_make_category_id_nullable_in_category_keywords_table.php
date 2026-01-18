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
        Schema::table('category_keywords', function (Blueprint $table) {
            // Drop foreign key first if it exists
            $table->dropForeign(['category_id']);

            // Make column nullable
            $table->unsignedBigInteger('category_id')->nullable()->change();

            // Re-add foreign key with set null on delete
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_keywords', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }
};
