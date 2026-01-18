<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\KeywordService;
use Illuminate\Support\Facades\Validator;

class KeywordController extends Controller
{
    protected $keywordService;

    public function __construct(KeywordService $keywordService)
    {
        $this->keywordService = $keywordService;
    }

    /**
     * Tüm keyword'leri listele
     * GET /api/keywords
     */
    public function index(): JsonResponse
    {
        $keywords = $this->keywordService->getAll();

        return response()->json([
            'success' => true,
            'data' => $keywords,
        ]);
    }

    /**
     * Kategoriye ait keyword'leri getir
     * GET /api/keywords/category/{id}
     */
    public function byCategoryId(int $id): JsonResponse
    {
        $keywords = $this->keywordService->getByCategoryId($id);

        return response()->json([
            'success' => true,
            'data' => $keywords,
        ]);
    }

    /**
     * Yeni keyword ekle
     * POST /api/keywords
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'keyword' => 'required|string|max:255',
            'match_type' => 'required|in:exact,contains,starts_with,ends_with,regex',
            'is_case_sensitive' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $keyword = $this->keywordService->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Keyword başarıyla oluşturuldu',
            'data' => $keyword,
        ], 201);
    }

    /**
     * Keyword güncelle
     * PUT /api/keywords/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|required|exists:categories,id',
            'keyword' => 'sometimes|required|string|max:255',
            'match_type' => 'sometimes|required|in:exact,contains,starts_with,ends_with,regex',
            'is_case_sensitive' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $keyword = $this->keywordService->update($id, $request->all());

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Keyword başarıyla güncellendi',
            'data' => $keyword,
        ]);
    }

    /**
     * Keyword sil
     * DELETE /api/keywords/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->keywordService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Keyword bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Keyword başarıyla silindi',
        ]);
    }

    /**
     * Toplu keyword import
     * POST /api/keywords/bulk-import
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'keywords' => 'required|array',
            'keywords.*.category_id' => 'required|exists:categories,id',
            'keywords.*.keyword' => 'required|string|max:255',
            'keywords.*.match_type' => 'required|in:exact,contains,starts_with,ends_with,regex',
            'keywords.*.is_case_sensitive' => 'nullable|boolean',
            'keywords.*.priority' => 'nullable|integer',
            'keywords.*.is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->keywordService->bulkImport($request->input('keywords'));

        return response()->json([
            'success' => true,
            'message' => "{$result['success']} keyword başarıyla import edildi, {$result['failed']} başarısız",
            'data' => $result,
        ]);
    }

    /**
     * Keyword test et
     * POST /api/keywords/test
     */
    public function test(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string',
            'text' => 'required|string',
            'match_type' => 'required|in:exact,contains,starts_with,ends_with,regex',
            'is_case_sensitive' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $matches = $this->keywordService->testMatch(
            $request->input('keyword'),
            $request->input('text'),
            $request->input('match_type'),
            $request->input('is_case_sensitive', false)
        );

        return response()->json([
            'success' => true,
            'matches' => $matches,
            'message' => $matches ? 'Eşleşme bulundu' : 'Eşleşme bulunamadı',
        ]);
    }
}
