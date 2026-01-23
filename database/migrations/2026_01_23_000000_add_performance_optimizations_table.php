<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Activities tablosuna index ekle
        Schema::table('activities', function (Blueprint $table) {
            $table->index('start_time_utc');
            $table->index('username');
            $table->index(['username', 'start_time_utc']);
        });

        // 2. Özet (Aggregation) Tablosu
        Schema::create('activity_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedTinyInteger('hour'); // 0-23
            $table->string('category_type'); // work, other, untagged
            $table->unsignedBigInteger('category_id')->nullable();
            $table->bigInteger('total_duration_ms')->default(0);
            $table->integer('activity_count')->default(0);

            $table->index(['date', 'category_type']);
            $table->index(['date', 'hour']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_summaries');
        
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['start_time_utc']);
            $table->dropIndex(['username']);
            $table->dropIndex(['username', 'start_time_utc']);
        });
    }
};
