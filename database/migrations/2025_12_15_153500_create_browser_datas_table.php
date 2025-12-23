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
        if (!Schema::hasTable('browser_datas')) {
            Schema::create('browser_datas', function (Blueprint $table) {
                $table->id();
                $table->text('url');
                $table->text('title')->nullable();
                $table->string('browser');
                $table->dateTime('visit_time_utc'>nullable();
                $table->dateTime('created_at_utc');
                $table->dateTime('received_at')->nullable()->useCurrent();

                $table->index('base_url');
                $table->index('browser');
                $table->index('motherboard_uuid');
                $table->index('user_sid');
                $table->string('base_url')->nullable();
                $table->string('username');
                $table->string('domain');
                $table->string('user_sid')->nullable();
                $table->string('motherboard_uuid')-
                $table->index('username');
                $table->index('visit_time_utc');
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
        Schema::dropIfExists('browser_datas');
    }
};
