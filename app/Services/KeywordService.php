<?php

namespace App\Services;

use App\Models\CategoryKeyword;
use Illuminate\Support\Collection;

class KeywordService
{
    /**
     * Tüm keyword'leri listele
     * 
     * @return Collection
     */
    public function getAll(): Collection
    {
        return CategoryKeyword::with('category')
            ->active()
            ->byPriority()
            ->get();
    }

    /**
     * Kategoriye ait keyword'leri getir
     * 
     * @param int $categoryId
     * @return Collection
     */
    public function getByCategoryId(int $categoryId): Collection
    {
        return CategoryKeyword::where('category_id', $categoryId)
            ->active()
            ->byPriority()
            ->get();
    }

    /**
     * Yeni keyword oluştur
     * 
     * @param array $data
     * @return CategoryKeyword
     */
    public function create(array $data): CategoryKeyword
    {
        return CategoryKeyword::create($data);
    }

    /**
     * Keyword güncelle
     * 
     * @param int $id
     * @param array $data
     * @return CategoryKeyword|null
     */
    public function update(int $id, array $data): ?CategoryKeyword
    {
        $keyword = CategoryKeyword::find($id);
        
        if (!$keyword) {
            return null;
        }
        
        $keyword->update($data);
        
        return $keyword->fresh();
    }

    /**
     * Keyword sil
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $keyword = CategoryKeyword::find($id);
        
        if (!$keyword) {
            return false;
        }
        
        return $keyword->delete();
    }

    /**
     * Toplu keyword import
     * 
     * @param array $data Array of keyword data
     * @return array Sonuçlar: ['success' => count, 'failed' => count, 'errors' => []]
     */
    public function bulkImport(array $data): array
    {
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($data as $index => $keywordData) {
            try {
                $this->create($keywordData);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'index' => $index,
                    'data' => $keywordData,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Keyword test et
     * 
     * @param string $keyword Keyword metni
     * @param string $text Test edilecek metin
     * @param string $matchType Eşleşme tipi
     * @param bool $isCaseSensitive Case sensitive mi?
     * @return bool Eşleşme var mı?
     */
    public function testMatch(string $keyword, string $text, string $matchType = 'contains', bool $isCaseSensitive = false): bool
    {
        // Geçici bir keyword objesi oluştur
        $tempKeyword = new CategoryKeyword([
            'keyword' => $keyword,
            'match_type' => $matchType,
            'is_case_sensitive' => $isCaseSensitive,
        ]);
        
        return $tempKeyword->matches($text);
    }
}
