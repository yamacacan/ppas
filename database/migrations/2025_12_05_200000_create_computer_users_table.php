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
        Schema::create('computer_users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('motherboard_uuid')->nullable();
            $table->string('name')->nullable(); // Display name (alias)
            $table->timestamps();

            $table->unique(['username', 'motherboard_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('computer_users');
    }
};
