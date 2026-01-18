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
        if (!Schema::hasTable('installed_apps')) {
            Schema::create('installed_apps', function (Blueprint $table) {
                $table->id();
                $table->string('app_name', 500);
                $table->string('username');
                $table->string('domain');
                $table->string('user_sid');
                $table->string('motherboard_uuid');
                $table->string('machine_name')->nullable();
                $table->dateTime('collected_at');

                $table->index('app_name');
                $table->index('username');
                $table->index('user_sid');
                $table->index('motherboard_uuid');
                $table->index('machine_name');
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
        Schema::dropIfExists('installed_apps');
    }
};
