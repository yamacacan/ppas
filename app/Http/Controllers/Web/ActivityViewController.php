<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AutoTaggingService;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\ActivitySummary;

class ActivityViewController extends Controller
{
    protected $autoTaggingService;

    public function __construct(AutoTaggingService $autoTaggingService)
    {
        $this->autoTaggingService = $autoTaggingService;
    }

    public function index(Request $request)
    {
        $query = Activity::with('categories')->withCount('categories');

        // Sorting
        $sortField = $request->get('sort_by', 'start_time_utc');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Allowed sort fields for security
        $allowedSorts = ['username', 'process_name', 'title', 'start_time_utc', 'duration_ms'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('start_time_utc', 'desc');
        }
        
        // Kategori filtresi
        if ($request->has('category_id') && $request->category_id) {
            $query->byCategory($request->category_id);
        }

        // Username filtresi
        if ($request->has('username') && $request->username) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        // Process search
        if ($request->has('process') && $request->process) {
            $query->where('process_name', 'like', '%' . $request->process . '%');
        }

        // Title search
        if ($request->has('title') && $request->title) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Tarih filtresi (Index dostu range sorgusu)
        if ($request->filled('start_date')) {
            $query->where('start_time_utc', '>=', $request->start_date . ' 00:00:00');
        }
        if ($request->filled('end_date')) {
            $query->where('start_time_utc', '<=', $request->end_date . ' 23:59:59');
        }

        // Durum filtresi (Tagged/Untagged)
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'tagged') {
                $query->has('categories');
            } elseif ($request->status === 'untagged') {
                $query->doesntHave('categories');
            }
        }
        
        // İstatistikler için Summary Table'ı kullanalım (Filtre yoksa çok hızlı gelir)
        // Eğer filtre varsa bu sayıları göstermek performansı çok düşürür.
        if (!$request->filled(['username', 'process', 'title', 'category_id'])) {
            $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());
            
            $summaryStats = ActivitySummary::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->whereNull('category_id')
                ->get();
            
            $taggedCount = $summaryStats->whereIn('category_type', ['work', 'other'])->sum('activity_count');
            $untaggedCount = $summaryStats->where('category_type', 'untagged')->sum('activity_count');
        } else {
            // Filtre varsa sadece cache'lenmiş bir tahmini sayı verelim veya -1 gönderelim
            $taggedCount = -1; 
            $untaggedCount = -1;
        }
        
        // Pagination: paginate(50) -> simplePaginate(50) 
        // 2 milyon veride COUNT(*) sorgusundan kurtuluruz.
        $activities = $query->simplePaginate(50)->withQueryString();
        $categories = Category::active()->get();
        
        return view('performance.activities.index', compact('activities', 'categories', 'taggedCount', 'untaggedCount'));
    }

    public function tagged(Request $request)
    {
        $query = Activity::tagged()
            ->with('categories');

        // Sorting
        $sortField = $request->get('sort_by', 'start_time_utc');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['username', 'process_name', 'title', 'start_time_utc', 'duration_ms'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('start_time_utc', 'desc');
        }

        $activities = $query->simplePaginate(50)->withQueryString();
        $categories = Category::active()->get();
        
        return view('performance.activities.tagged', compact('activities', 'categories'));
    }

    public function untagged(Request $request)
    {
        $query = Activity::untagged();

        // Sorting
        $sortField = $request->get('sort_by', 'start_time_utc');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['username', 'process_name', 'title', 'start_time_utc', 'duration_ms'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('start_time_utc', 'desc');
        }

        $activities = $query->simplePaginate(50)->withQueryString();
        $categories = Category::active()->get();
        
        // Total count'ı summary table üzerinden hızlıca alalım
        $totalCount = ActivitySummary::where('category_type', 'untagged')->whereNull('category_id')->sum('activity_count');
        
        return view('performance.activities.untagged', compact('activities', 'categories', 'totalCount'));
    }

    public function autoTagPage()
    {
        $untaggedCount = Activity::untagged()->count();
        $taggedCount = Activity::tagged()->count();
        $totalCount = Activity::count();
        
        return view('performance.activities.auto-tag', compact('untaggedCount', 'taggedCount', 'totalCount'));
    }

    public function autoTagRun(Request $request)
    {
        $limit = $request->get('limit', 100);
        
        $result = $this->autoTaggingService->tagUntaggedActivities($limit);
        
        return redirect()->route('activities.auto-tag')
            ->with('success', "Otomatik tagleme tamamlandı. İşlenen: {$result['processed']}, Taglenen: {$result['tagged']}");
    }

    public function show($id)
    {
        $activity = Activity::with('categories')->findOrFail($id);
        return view('performance.activities.show', compact('activity'));
    }
}
