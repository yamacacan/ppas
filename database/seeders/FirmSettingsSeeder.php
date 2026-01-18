<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FirmSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if settings already exist
        if (DB::table('firm_settings')->count() === 0) {
            DB::table('firm_settings')->insert([
                'firm_name' => 'My Company',
                'address' => 'Company Address',
                'email' => 'info@example.com',
                'work_start_time' => '09:00:00',
                'work_end_time' => '18:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
