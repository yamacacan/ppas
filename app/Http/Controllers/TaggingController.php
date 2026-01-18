<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AutoTaggingService;
use App\Models\Activity;
use Illuminate\Support\Facades\Validator;

class TaggingController extends Controller
{
    protected $autoTaggingService;

    public function __construct(AutoTaggingService $autoTaggingService)
    {
        $this->autoTaggingService = $autoTaggingService;
    }

    /**
     * Aktiviteye manuel kategori tagle
     * POST /api/activities/{id}/tag
     */
    public function tagManually(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $activity = Activity::find($id);

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivite bulunamadı',
            ], 404);
        }

        $activity->tagManually($request->input('category_id'), $request->input('note'));

        return response()->json([
            'success' => true,
            'message' => 'Aktivite başarıyla taglendi',
        ]);
    }

    /**
     * Aktiviteden kategori kaldır
     * DELETE /api/activities/{id}/tag/{categoryId}
     */
    public function removeTag(int $id, int $categoryId): JsonResponse
    {
        $activity = Activity::find($id);

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Aktivite bulunamadı',
            ], 404);
        }

        $activity->removeTag($categoryId);

        return response()->json([
            'success' => true,
            'message' => 'Tag başarıyla kaldırıldı',
        ]);
    }

    /**
     * Tüm taglenmemiş aktiviteleri otomatik tagle
     * POST /api/activities/auto-tag
     */
    public function autoTagAll(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 100);

        $result = $this->autoTaggingService->tagUntaggedActivities($limit);

        return response()->json([
            'success' => true,
            'message' => "{$result['tagged']} aktivite başarıyla taglendi",
            'data' => $result,
        ]);
    }

    /**
     * Belirli aktiviteyi otomatik tagle
     * POST /api/activities/auto-tag/{id}
     */
    public function autoTagSingle(int $id): JsonResponse
    {
        $result = $this->autoTaggingService->tagActivity($id);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Taglenmiş aktiviteleri getir
     * GET /api/activities/tagged
     */
    public function getTagged(Request $request): JsonResponse
    {
        $query = Activity::tagged()
            ->with('categories')
            ->orderBy('start_time_utc', 'desc');

        // Pagination
        $perPage = $request->input('per_page', 50);
        $activities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    /**
     * Taglenmemiş aktiviteleri getir
     * GET /api/activities/untagged
     */
    public function getUntagged(Request $request): JsonResponse
    {
        $query = Activity::untagged()
            ->orderBy('start_time_utc', 'desc');

        // Pagination
        $perPage = $request->input('per_page', 50);
        $activities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    /**
     * Kategoriye göre aktiviteleri getir
     * GET /api/activities/category/{categoryId}
     */
    public function getByCategory(int $categoryId, Request $request): JsonResponse
    {
        $query = Activity::byCategory($categoryId)
            ->with('categories')
            ->orderBy('start_time_utc', 'desc');

        // Pagination
        $perPage = $request->input('per_page', 50);
        $activities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
