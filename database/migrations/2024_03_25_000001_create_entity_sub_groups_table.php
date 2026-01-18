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
        Schema::create('entity_sub_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_group_id')->constrained('entity_main_groups')->onDelete('CASCADE');
            $table->string('name');
            $table->string('group_no');
            $table->integer('group_order_no');
            $table->string('degree_of_criticality')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_sub_groups');
    }
};


