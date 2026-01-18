<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SidebarMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[

            [1,0,'Yönetimsel İşlemler',1],
            

            [2,0,'Varlık İşlemleri',2],
                [3,2,'Varlık Grubu İşlemleri',1],
                [4,2,'Varlık İşlemleri',2],
                [5,2,'Anket İşlemleri',3],
                [6,2,'Uygulanabilirlik Bildirgesi',4],
                [7,2,'Boşluk Analizi',5],
                [8,2,'Telafi Edici Kontrol Formları',6],
                [9,2,'İş Paketleri',7],

            [10,0,'Tedbir İşlemleri',3],
                [11,10,'Tedbir Ana Başlıkları',1],
                [12,10,'Tedbir Alt Başlıkları',2],
                [13,10,'Tedbirler' ,3],

            [14,0,'Denetim İşlemleri',4],
                [15,14,'Denetçi İşlemleri',1],
                [16,14,'Bilgi Alınan Personel' ,2],
                [17,14,'Denetim Oluştur',3],
                [18,14,'Denetime Denetçi Ata',4],
                [19,14,'Denetime Personel Ata',5],
                [20,14,'Denetime Varlık Ata',6],
                [21,14,'Denetim Programı',7],
                [23,14,'Tedbir Etkinlik Durumu',8],
                [22,14,'Uygulama Süreci Değerlendir',9],
                [29,14,'Bulgular',10],
               

            [25,0,'Rapor İşlemleri',5],
                [26,25,'Raporlar',1],
                [24,25,'Çalışma Formları',2],

            [27,0,'Servis İşlemleri',6],
                [28,27,'Servis İstekleri',1],




           

        ];

        DB::table('sidebar_menus')->truncate();

        foreach ($data as $item) {
            DB::table('sidebar_menus')->insert([
                'id'=>$item[0],
                'parent_id'=>$item[1],
                'name' => $item[2],
                'order'=>$item[3],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
