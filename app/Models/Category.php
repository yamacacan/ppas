<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'level',
        'type',
        'color',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    // İlişkiler
    
    /**
     * Parent kategori ilişkisi
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Alt kategoriler ilişkisi
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Kategori keyword'leri ilişkisi
     */
    public function keywords()
    {
        return $this->hasMany(CategoryKeyword::class);
    }

    /**
     * Kategoriye ait aktiviteler (many-to-many)
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_categories')
            ->withPivot('matched_keyword', 'match_type', 'confidence_score', 'is_manual', 'tagged_at')
            ->withTimestamps();
    }

    // Scope'lar

    /**
     * Sadece aktif kategorileri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Tipine göre kategorileri filtrele (work/other)
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Sadece ana kategorileri getir (level 1)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id')->orWhere('level', 1);
    }

    // Helper Metodlar

    /**
     * Kategori yolunu string olarak döndürür
     * Örn: "İş > Yazılım Geliştirme > Web Geliştirme"
     */
    public function getFullPath(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Tüm alt kategorileri recursive olarak getirir
     */
    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    /**
     * Bu kategorinin verilen kategorinin parent'ı olup olmadığını kontrol eder
     */
    public function isParentOf(Category $category): bool
    {
        if ($category->parent_id === $this->id) {
            return true;
        }

        if ($category->parent) {
            return $this->isParentOf($category->parent);
        }

        return false;
    }

    /**
     * Bu kategorinin verilen kategorinin alt kategorisi (descendant) olup olmadığını kontrol eder
     * Circular reference'ları önlemek için kullanılır
     */
    public function isDescendantOf(Category $category): bool
    {
        if ($this->parent_id === $category->id) {
            return true;
        }

        if ($this->parent) {
            return $this->parent->isDescendantOf($category);
        }

        return false;
    }

    /**
     * Boot method - slug otomatik oluşturma
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }
}
