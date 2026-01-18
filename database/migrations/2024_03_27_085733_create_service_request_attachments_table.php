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
        Schema::create('service_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('id')->on('service_requests')->onDelete('CASCADE');

            $table->string('file_name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request_attachments');
    }
};
