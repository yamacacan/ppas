<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryService
{
    /**
     * Tüm kategorileri ağaç yapısında listele
     * 
     * @param string|null $type Kategori tipi (work/other), null ise hepsi
     * @return Collection
     */
    public function getAll(?string $type = null): Collection
    {
        $query = Category::with(['children', 'parent'])->active();
        
        if ($type) {
            $query->byType($type);
        }
        
        $categories = $query->orderBy('sort_order')->get();
        
        return $this->buildTree($categories);
    }

    /**
     * Kategori detayını getir
     * 
     * @param int $id
     * @return Category|null
     */
    public function getById(int $id): ?Category
    {
        return Category::with(['parent', 'children', 'keywords'])->find($id);
    }

    /**
     * Yeni kategori oluştur
     * 
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        // Parent varsa level'i hesapla
        if (!empty($data['parent_id'])) {
            $parent = Category::find($data['parent_id']);
            if ($parent) {
                $data['level'] = $parent->level + 1;
            }
        }
        
        return Category::create($data);
    }

    /**
     * Kategori güncelle
     * 
     * @param int $id
     * @param array $data
     * @return Category|null
     */
    public function update(int $id, array $data): ?Category
    {
        $category = Category::find($id);
        
        if (!$category) {
            return null;
        }
        
        // Parent değiştiyse level'i yeniden hesapla
        if (isset($data['parent_id']) && $data['parent_id'] !== $category->parent_id) {
            if ($data['parent_id']) {
                $parent = Category::find($data['parent_id']);
                if ($parent) {
                    $data['level'] = $parent->level + 1;
                }
            } else {
                $data['level'] = 1;
            }
        }
        
        $category->update($data);
        
        return $category->fresh();
    }

    /**
     * Kategori sil
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $category = Category::find($id);
        
        if (!$category) {
            return false;
        }
        
        // Alt kategori var mı kontrol et
        if ($category->children()->count() > 0) {
            throw new \Exception('Bu kategorinin alt kategorileri var. Önce alt kategorileri silmelisiniz veya taşımalısınız.');
        }

        // Kategoriye bağlı keywordlerin category_id'sini null yap (Orphan keywords)
        $category->keywords()->update(['category_id' => null]);

        // Kategori sil
        return $category->delete();
    }

    /**
     * Alt kategorileri getir
     * 
     * @param int $id
     * @return Collection
     */
    public function getChildren(int $id): Collection
    {
        $category = Category::find($id);
        
        if (!$category) {
            return collect();
        }
        
        return $category->children()->active()->orderBy('sort_order')->get();
    }

    /**
     * Kategori ağacını oluştur (recursive)
     * 
     * @param Collection $categories
     * @param int|null $parentId
     * @return Collection
     */
    public function buildTree(Collection $categories, ?int $parentId = null): Collection
    {
        $tree = collect();
        
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $category->children_tree = $this->buildTree($categories, $category->id);
                $tree->push($category);
            }
        }
        
        return $tree;
    }

    /**
     * Kategori tipine göre kategorileri getir
     * 
     * @param string $type
     * @return Collection
     */
    public function getByType(string $type): Collection
    {
        return $this->getAll($type);
    }
}
