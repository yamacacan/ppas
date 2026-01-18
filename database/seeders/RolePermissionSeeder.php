<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Role::updateOrCreate([
            'name' => 'Admin',

        ]);



        
        Role::updateOrCreate([
            'name' => 'Super Admin',

        ]);

        $role= Role::findByName('Super Admin');


    }
}
