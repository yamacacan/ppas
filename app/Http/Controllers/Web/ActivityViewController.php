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
        $query = Activity::query()
            ->select('activities.*', 'computer_users.name as computer_user_display_name')
            ->leftJoin('computer_users', function($join) {
                $join->on('activities.username', '=', 'computer_users.username')
                     ->on('activities.motherboard_uuid', '=', 'computer_users.motherboard_uuid');
            })
            ->with('categories')
            ->withCount('categories');

        // Sorting
        $sortField = $request->get('sort_by', 'start_time_utc');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Allowed sort fields for security
        $allowedSorts = ['username', 'process_name', 'title', 'start_time_utc', 'duration_ms'];
        $actualSortField = $sortField;
        if ($sortField === 'username') $actualSortField = 'activities.username';

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($actualSortField, $sortOrder);
        } else {
            $query->orderBy('activities.start_time_utc', 'desc');
        }
        
        // Kategori filtresi
        if ($request->has('category_id') && $request->category_id) {
            $query->byCategory($request->category_id);
        }

        // Username filtresi - Artık tekil kimlik (username:uuid) gelebilir
        if ($request->filled('username')) {
            $val = $request->username;
            if (str_contains($val, '|')) {
                [$u, $uuid] = explode('|', $val);
                $query->where('activities.username', $u)
                      ->where('activities.motherboard_uuid', $uuid);
            } else {
                $query->where('activities.username', 'like', '%' . $val . '%');
            }
        }

        // Process search
        if ($request->filled('process')) {
            $query->where('activities.process_name', 'like', '%' . $request->process . '%');
        }

        // Title search
        if ($request->filled('title')) {
            $query->where('activities.title', 'like', '%' . $request->title . '%');
        }

        // Tarih filtresi
        if ($request->filled('start_date')) {
            $query->where('activities.start_time_utc', '>=', $request->start_date . ' 00:00:00');
        }
        if ($request->filled('end_date')) {
            $query->where('activities.start_time_utc', '<=', $request->end_date . ' 23:59:59');
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
        if (!$request->filled(['username', 'process', 'title', 'category_id'])) {
            $startDate = $request->input('start_date') ?: now()->subDays(30)->toDateString();
            $endDate = $request->input('end_date') ?: now()->toDateString();
            
            $summaryStats = ActivitySummary::where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->whereNull('category_id')
                ->get();
            
            $taggedCount = $summaryStats->whereIn('category_type', ['work', 'other'])->sum('activity_count');
            $untaggedCount = $summaryStats->where('category_type', 'untagged')->sum('activity_count');
        } else {
            $taggedCount = -1; 
            $untaggedCount = -1;
        }
        
        $activities = $query->simplePaginate(50)->withQueryString();
        $categories = Category::active()->get();
        $computerUsers = \App\Models\ComputerUser::orderBy('name')->get();
        
        return view('performance.activities.index', compact('activities', 'categories', 'computerUsers', 'taggedCount', 'untaggedCount'));
    }

    public function tagged(Request $request)
    {
        $query = Activity::tagged()
            ->select('activities.*', 'computer_users.name as computer_user_display_name')
            ->leftJoin('computer_users', function($join) {
                $join->on('activities.username', '=', 'computer_users.username')
                     ->on('activities.motherboard_uuid', '=', 'computer_users.motherboard_uuid');
            })
            ->with('categories');

        // Sorting
        $sortField = $request->get('sort_by', 'start_time_utc');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['username', 'process_name', 'title', 'start_time_utc', 'duration_ms'];
        if (in_array($sortField, $allowedSorts)) {
            $actualSortField = ($sortField === 'username') ? 'activities.username' : $sortField;
            $query->orderBy($actualSortField, $sortOrder);
        } else {
            $query->orderBy('activities.start_time_utc', 'desc');
        }

        $activities = $query->simplePaginate(50)->withQueryString();
        $categories = Category::active()->get();
        $computerUsers = \App\Models\ComputerUser::orderBy('name')->get();
        
        return view('performance.activities.tagged', compact('activities', 'categories', 'computerUsers'));
    }

    public function untagged(Request $request)
    {
        $query = Activity::untagged()
            ->select('activities.*', 'computer_users.name as computer_user_display_name')
            ->leftJoin('computer_users', function($join) {
                $join->on('activities.username', '=', 'computer_users.username')
                     ->on('activities.motherboard_uuid', '=', 'computer_users.motherboard_uuid');
            });

        // Sorting
        $sortField = $request->get('sort_by', 'start_time_utc');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['username', 'process_name', 'title', 'start_time_utc', 'duration_ms'];
        if (in_array($sortField, $allowedSorts)) {
            $actualSortField = ($sortField === 'username') ? 'activities.username' : $sortField;
            $query->orderBy($actualSortField, $sortOrder);
        } else {
            $query->orderBy('activities.start_time_utc', 'desc');
        }

        $activities = $query->simplePaginate(50)->withQueryString();
        $categories = Category::active()->get();
        $computerUsers = \App\Models\ComputerUser::orderBy('name')->get();
        
        // Total count'ı summary table üzerinden hızlıca alalım
        $totalCount = ActivitySummary::where('category_type', 'untagged')->whereNull('category_id')->sum('activity_count');
        
        return view('performance.activities.untagged', compact('activities', 'categories', 'computerUsers', 'totalCount'));
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
