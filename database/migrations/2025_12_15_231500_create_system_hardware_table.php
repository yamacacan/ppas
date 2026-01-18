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
        if (!Schema::hasTable('system_hardware')) {
            Schema::create('system_hardware', function (Blueprint $table) {
                $table->id();
                $table->string('hostname');
                $table->string('username');
                $table->string('domain');
                $table->string('user_sid');
                $table->string('os_version')->nullable();
                $table->string('model', 500)->nullable();
                $table->string('serial_number')->nullable();
                $table->string('motherboard_uuid')->nullable();
                $table->decimal('disk_total_gb', 10, 2)->nullable();
                $table->decimal('disk_used_gb', 10, 2)->nullable();
                $table->decimal('disk_free_gb', 10, 2)->nullable();
                $table->decimal('disk_usage_percent', 5, 2)->nullable();
                $table->decimal('ram_total_gb', 10, 2)->nullable();
                $table->decimal('ram_used_gb', 10, 2)->nullable();
                $table->decimal('ram_free_gb', 10, 2)->nullable();
                $table->decimal('ram_usage_percent', 5, 2)->nullable();
                $table->dateTime('collected_at');

                $table->index('hostname');
                $table->index('username');
                $table->index('user_sid');
                $table->index('motherboard_uuid');
                $table->index('collected_at');
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
        Schema::dropIfExists('system_hardware');
    }
};
