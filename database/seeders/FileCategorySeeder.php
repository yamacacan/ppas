<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('file_category')->insert([
            ['text'=>'Politikalar','prefix'=>'POL','created_at'=>now()],
            ['text'=>'Prosedürler','prefix'=>'PRO','created_at'=>now()],
            ['text'=>'Talimatlar','prefix'=>'TAL','created_at'=>now()],
            ['text'=>'Görev Tanımları','prefix'=>'GT','created_at'=>now()],
            ['text'=>'Formlar','prefix'=>'F','created_at'=>now()],
        ]);
    }
}
