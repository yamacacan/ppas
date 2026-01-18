<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ModelUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('model_has_roles')->insert([
            
            'role_id' => 1,
            'model_type'=>'app\models\user',
            'model_id'=>1,
            
            
        ]);
    }
}
