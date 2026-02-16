<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_summaries', function (Blueprint $table) {
            $table->string('username')->nullable()->after('hour');
            $table->string('motherboard_uuid')->nullable()->after('username');
            $table->boolean('is_manual')->default(false)->after('category_id');
            
            $table->index(['username', 'date']);
            $table->index(['motherboard_uuid', 'date']);
        });

        Schema::table('process_summaries', function (Blueprint $table) {
            $table->string('username')->nullable()->after('date');
            $table->string('motherboard_uuid')->nullable()->after('username');
            
            $table->index(['username', 'date']);
            $table->index(['motherboard_uuid', 'date']);
        });

        Schema::table('keyword_summaries', function (Blueprint $table) {
            $table->string('username')->nullable()->after('date');
            $table->string('motherboard_uuid')->nullable()->after('username');
            $table->bigInteger('total_duration_ms')->default(0)->after('match_count');
            
            $table->index(['username', 'date']);
            $table->index(['motherboard_uuid', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('activity_summaries', function (Blueprint $table) {
            $table->dropColumn(['username', 'motherboard_uuid', 'is_manual']);
        });

        Schema::table('process_summaries', function (Blueprint $table) {
            $table->dropColumn(['username', 'motherboard_uuid']);
        });

        Schema::table('keyword_summaries', function (Blueprint $table) {
            $table->dropColumn(['username', 'motherboard_uuid', 'total_duration_ms']);
        });
    }
};
