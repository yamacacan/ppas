<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\PrecautionMainTitles;
use App\Models\PrecautionTitles;
use Illuminate\Console\Application;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            
            UnitSeeder::class,
          
            RolePermissionSeeder::class,
            
          
           
            
          
            PermissionSeeder::class,
            ServiceRequestFilterSeeder::class,
            SidebarMenuSeeder::class,
                   
            UserSeeder::class,
            ModelUserSeeder::class,
            
            FileCategorySeeder::class,
            
            // Performance Agent Seeders
            CategorySeeder::class,
            CategoryKeywordSeeder::class,
            FirmSettingsSeeder::class,

        ]);
            
    }
}
