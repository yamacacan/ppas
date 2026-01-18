<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Validator;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Kategori istatistikleri
     * GET /api/statistics/categories
     */
    public function categories(Request $request): JsonResponse
    {
        $filters = $request->only(['start_date', 'end_date', 'username', 'activity_type']);
        
        $stats = $this->statisticsService->getCategoryStatistics($filters);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Belirli kategori istatistikleri
     * GET /api/statistics/category/{id}
     */
    public function category(Request $request, int $id): JsonResponse
    {
        $filters = $request->only(['start_date', 'end_date', 'username', 'activity_type']);
        
        $stats = $this->statisticsService->getCategoryById($id, $filters);

        if (!$stats) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Tagleme başarı oranı
     * GET /api/statistics/tagging-rate
     */
    public function taggingRate(Request $request): JsonResponse
    {
        $filters = $request->only(['start_date', 'end_date', 'username', 'activity_type']);
        
        $stats = $this->statisticsService->getTaggingRate($filters);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Zaman dilimine göre kategori dağılımı
     * GET /api/statistics/time-distribution
     */
    public function timeDistribution(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'group_by' => 'nullable|in:day,week,month',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $stats = $this->statisticsService->getTimeDistribution(
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('group_by', 'day')
        );

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * İş/Diğer dağılımı
     * GET /api/statistics/work-other-ratio
     */
    public function workOtherRatio(Request $request): JsonResponse
    {
        $filters = $request->only(['start_date', 'end_date', 'username', 'activity_type']);
        
        $stats = $this->statisticsService->getWorkOtherRatio($filters);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
