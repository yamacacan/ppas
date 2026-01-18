<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AuditorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $auditorRoles=[

            ['Denetçi', 'D'],
            ['Başdenetçi', 'BD'],
            ['Uzman', 'U'],
            ['Denetim Koordinatörü', 'DK'],
        ];

        foreach ($auditorRoles as $auditorRole) {
            DB::table('auditor_roles')->insert([
                'role'=>$auditorRole[0],
                'role_slug' => $auditorRole[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
