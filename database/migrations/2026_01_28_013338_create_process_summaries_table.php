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
        Schema::create('process_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('process_name');
            $table->bigInteger('total_duration_ms')->default(0);
            $table->integer('activity_count')->default(0);

            $table->index(['date', 'process_name']);
            $table->index('process_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_summaries');
    }
};
