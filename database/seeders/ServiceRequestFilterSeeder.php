<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceRequestFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requests = [
            ['1','Bekleyen istekler'],
            ['2','Cevap bekleyen istekler'],
            ['3','İşleme alınan istekler'],
            ['4','Çözülmüş istekler'],
            ['5','Silinmiş istekler']

        ];

        foreach ($requests as $item) {
            DB::table('service_request_filters')->insert([
                'id'=> $item[0],
                'name' => $item[1],              
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
