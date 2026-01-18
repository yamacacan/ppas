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
        Schema::create('firm_settings', function (Blueprint $table) {
            $table->id();
            $table->string('firm_name')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->time('work_start_time')->default('09:00:00');
            $table->time('work_end_time')->default('18:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firm_settings');
    }
};
