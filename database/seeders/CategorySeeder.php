<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ana Kategoriler
        
        // İş Kategorisi
        $work = Category::create([
            'name' => 'İş',
            'slug' => 'is',
            'type' => 'work',
            'level' => 1,
            'color' => '#2563eb',
            'icon' => 'briefcase',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Diğer Kategorisi
        $other = Category::create([
            'name' => 'Diğer',
            'slug' => 'diger',
            'type' => 'other',
            'level' => 1,
            'color' => '#64748b',
            'icon' => 'circle',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // İŞ Alt Kategorileri
        
        // Yazılım Geliştirme
        $softwareDev = Category::create([
            'name' => 'Yazılım Geliştirme',
            'slug' => 'yazilim-gelistirme',
            'parent_id' => $work->id,
            'type' => 'work',
            'level' => 2,
            'color' => '#3b82f6',
            'icon' => 'code',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create(['name' => 'Web Geliştirme', 'slug' => 'web-gelistirme', 'parent_id' => $softwareDev->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Frontend', 'slug' => 'frontend', 'parent_id' => $softwareDev->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 2]);
        Category::create(['name' => 'Backend', 'slug' => 'backend', 'parent_id' => $softwareDev->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 3]);
        Category::create(['name' => 'Mobil Uygulama', 'slug' => 'mobil-uygulama', 'parent_id' => $softwareDev->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 4]);
        Category::create(['name' => 'Masaüstü Uygulama', 'slug' => 'masaustu-uygulama', 'parent_id' => $softwareDev->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 5]);

        // Veri Analizi
        $dataAnalysis = Category::create([
            'name' => 'Veri Analizi',
            'slug' => 'veri-analizi',
            'parent_id' => $work->id,
            'type' => 'work',
            'level' => 2,
            'color' => '#10b981',
            'icon' => 'chart-bar',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create(['name' => 'Excel/Spreadsheet', 'slug' => 'excel-spreadsheet', 'parent_id' => $dataAnalysis->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Database İşlemleri', 'slug' => 'database-islemleri', 'parent_id' => $dataAnalysis->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 2]);

        // İletişim
        $communication = Category::create([
            'name' => 'İletişim',
            'slug' => 'iletisim',
            'parent_id' => $work->id,
            'type' => 'work',
            'level' => 2,
            'color' => '#f59e0b',
            'icon' => 'mail',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Category::create(['name' => 'Email', 'slug' => 'email', 'parent_id' => $communication->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Mesajlaşma', 'slug' => 'mesajlasma', 'parent_id' => $communication->id, 'type' => 'work', 'level' => 3, 'is_active' => true, 'sort_order' => 2]);

        // Dokümantasyon
        Category::create([
            'name' => 'Dokümantasyon',
            'slug' => 'dokumantasyon',
            'parent_id' => $work->id,
            'type' => 'work',
            'level' => 2,
            'color' => '#8b5cf6',
            'icon' => 'document-text',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // DİĞER Alt Kategorileri
        
        // Eğlence
        $entertainment = Category::create([
            'name' => 'Eğlence',
            'slug' => 'eglence',
            'parent_id' => $other->id,
            'type' => 'other',
            'level' => 2,
            'color' => '#ec4899',
            'icon' => 'play',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create(['name' => 'Oyunlar', 'slug' => 'oyunlar', 'parent_id' => $entertainment->id, 'type' => 'other', 'level' => 3, 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Video İzleme', 'slug' => 'video-izleme', 'parent_id' => $entertainment->id, 'type' => 'other', 'level' => 3, 'is_active' => true, 'sort_order' => 2]);

        // Sosyal Medya
        $socialMedia = Category::create([
            'name' => 'Sosyal Medya',
            'slug' => 'sosyal-medya',
            'parent_id' => $other->id,
            'type' => 'other',
            'level' => 2,
            'color' => '#06b6d4',
            'icon' => 'share',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create(['name' => 'Facebook', 'slug' => 'facebook', 'parent_id' => $socialMedia->id, 'type' => 'other', 'level' => 3, 'is_active' => true, 'sort_order' => 1]);
        Category::create(['name' => 'Twitter', 'slug' => 'twitter', 'parent_id' => $socialMedia->id, 'type' => 'other', 'level' => 3, 'is_active' => true, 'sort_order' => 2]);
        Category::create(['name' => 'Instagram', 'slug' => 'instagram', 'parent_id' => $socialMedia->id, 'type' => 'other', 'level' => 3, 'is_active' => true, 'sort_order' => 3]);

        // Müzik
        Category::create(['name' => 'Müzik', 'slug' => 'muzik', 'parent_id' => $other->id, 'type' => 'other', 'level' => 2, 'color' => '#a855f7', 'icon' => 'musical-note', 'is_active' => true, 'sort_order' => 3]);

        // Haber
        Category::create(['name' => 'Haber', 'slug' => 'haber', 'parent_id' => $other->id, 'type' => 'other', 'level' => 2, 'color' => '#6b7280', 'icon' => 'newspaper', 'is_active' => true, 'sort_order' => 4]);
    }
}
