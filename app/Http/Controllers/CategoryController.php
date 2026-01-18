<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Tüm kategorileri listele (ağaç yapısında)
     * GET /api/categories
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->query('type'); // work, other veya null
        $categories = $this->categoryService->getAll($type);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Kategori detayı
     * GET /api/categories/{id}
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Yeni kategori oluştur
     * POST /api/categories
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:work,other',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = $this->categoryService->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla oluşturuldu',
            'data' => $category,
        ], 201);
    }

    /**
     * Kategori güncelle
     * PUT /api/categories/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'sometimes|in:work,other',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = $this->categoryService->update($id, $request->all());

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla güncellendi',
            'data' => $category,
        ]);
    }

    /**
     * Kategori sil
     * DELETE /api/categories/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->categoryService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori başarıyla silindi',
        ]);
    }

    /**
     * Alt kategorileri getir
     * GET /api/categories/{id}/children
     */
    public function children(int $id): JsonResponse
    {
        $children = $this->categoryService->getChildren($id);

        return response()->json([
            'success' => true,
            'data' => $children,
        ]);
    }

    /**
     * Tipine göre kategorileri getir
     * GET /api/categories/type/{type}
     */
    public function byType(string $type): JsonResponse
    {
        if (!in_array($type, ['work', 'other'])) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz kategori tipi',
            ], 400);
        }

        $categories = $this->categoryService->getByType($type);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
