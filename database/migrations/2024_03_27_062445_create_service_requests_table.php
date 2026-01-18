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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('id')->on('entity_sub_groups')->onDelete('CASCADE');
            $table->smallInteger('service_type');
            $table->text('title');
            $table->text('description');
            $table->smallInteger('level');
            $table->foreignId('request_filter_id')->constrained('id')->on('service_request_filters')->onDelete('RESTRICT');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
