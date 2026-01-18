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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('id')->on('users')->onDelete('CASCADE');
            $table->foreignId('unit_id')->constrained('id')->on('units')->onDelete('RESTRICT');
            $table->foreignId('title_id')->constrained('id')->on('titles')->onDelete('RESTRICT');


            $table->string('phone');
           

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
