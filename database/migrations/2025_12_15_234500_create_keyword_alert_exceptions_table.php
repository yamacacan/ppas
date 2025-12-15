<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('keyword_alert_exceptions')) {
            Schema::create('keyword_alert_exceptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('keyword_id')->constrained('category_keywords')->onDelete('cascade');
                $table->foreignId('computer_user_id')->nullable()->constrained('computer_users')->onDelete('cascade');
                $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('cascade');
                $table->timestamps();

                // Birim veya User'dan sadece biri dolu olmalı (Business Logic, DB constraint opsiyonel)
                $table->index('keyword_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keyword_alert_exceptions');
    }
};
