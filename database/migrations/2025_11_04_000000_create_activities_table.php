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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('evt')->nullable();
            $table->string('activity_type');
            $table->string('process_name');
            $table->string('title')->nullable();
            $table->dateTime('start_time_utc');
            $table->dateTime('end_time_utc')->nullable();
            $table->integer('duration_ms')->default(0);
            $table->string('username');
            $table->string('domain')->nullable();
            $table->string('user_sid')->nullable();
            $table->string('motherboard_uuid')->nullable();
            $table->text('url')->nullable();
            $table->string('browser')->nullable();
            $table->string('base_url')->nullable();
            $table->dateTime('created_at_utc');
            $table->dateTime('received_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
