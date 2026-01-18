<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\CategoryKeyword;

class CategoryKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        // Yazılım Geliştirme Keywords
        $this->createKeywords($categories->get('yazilim-gelistirme')?->id, [
            ['keyword' => 'Visual Studio Code', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'Code.exe', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'vscode', 'match_type' => 'contains', 'priority' => 9],
            ['keyword' => 'github', 'match_type' => 'contains', 'priority' => 6],
        ]);

        // Veri Analizi Keywords
        $this->createKeywords($categories->get('veri-analizi')?->id, [
            ['keyword' => 'Excel', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'EXCEL.EXE', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'spreadsheet', 'match_type' => 'contains', 'priority' => 8],
        ]);

        // İletişim Keywords
        $this->createKeywords($categories->get('iletisim')?->id, [
            ['keyword' => 'Outlook', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'Gmail', 'match_type' => 'contains', 'priority' => 9],
            ['keyword' => 'Slack', 'match_type' => 'contains', 'priority' => 9],
            ['keyword' => 'Teams', 'match_type' => 'contains', 'priority' => 9],
        ]);

        // Dokümantasyon Keywords
        $this->createKeywords($categories->get('dokumantasyon')?->id, [
            ['keyword' => 'Word', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'WINWORD.EXE', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'Google Docs', 'match_type' => 'contains', 'priority' => 9],
        ]);

        // Eğlence Keywords
        $this->createKeywords($categories->get('eglence')?->id, [
            ['keyword' => 'YouTube', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'Netflix', 'match_type' => 'contains', 'priority' => 9],
            ['keyword' => 'Steam', 'match_type' => 'contains', 'priority' => 8],
        ]);

        // Sosyal Medya Keywords
        $this->createKeywords($categories->get('sosyal-medya')?->id, [
            ['keyword' => 'Facebook', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'Twitter', 'match_type' => 'contains', 'priority' => 10],
            ['keyword' => 'Instagram', 'match_type' => 'contains', 'priority' => 10],
        ]);
    }

    private function createKeywords(?int $categoryId, array $keywords): void
    {
        if (!$categoryId) return;
        
        foreach ($keywords as $kw) {
            CategoryKeyword::create([
                'category_id' => $categoryId,
                'keyword' => $kw['keyword'],
                'match_type' => $kw['match_type'],
                'priority' => $kw['priority'],
                'is_case_sensitive' => $kw['is_case_sensitive'] ?? false,
                'is_active' => true,
            ]);
        }
    }
}
