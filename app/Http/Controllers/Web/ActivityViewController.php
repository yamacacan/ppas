<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AutoTaggingService;
use App\Models\Activity;
use App\Models\Category;
use Illuminate\Http\Request;

class ActivityViewController extends Controller
{
    protected $autoTaggingService;

    public function __construct(AutoTaggingService $autoTaggingService)
    {
        $this->autoTaggingService = $autoTaggingService;
    }

    public function index(Request $request)
    {
        $query = Activity::with('categories')->withCount('categories')->orderBy('start_time_utc', 'desc');
        
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

        // Tarih filtresi
        if ($request->filled('start_date')) {
            $query->whereDate('start_time_utc', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('start_time_utc', '<=', $request->end_date);
        }

        // Durum filtresi (Tagged/Untagged)
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'tagged') {
                $query->has('categories');
            } elseif ($request->status === 'untagged') {
                $query->doesntHave('categories');
            }
        }
        
        // İstatistikler için (filtrelere bağlı kalmadan genel istatistikler)
        $statsQuery = Activity::query();
        if ($request->filled('start_date')) {
            $statsQuery->whereDate('start_time_utc', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $statsQuery->whereDate('start_time_utc', '<=', $request->end_date);
        }

        $taggedCount = (clone $statsQuery)->has('categories')->count();
        $untaggedCount = (clone $statsQuery)->doesntHave('categories')->count();
        
        // Pagination kullan
        $activities = $query->get();
        $categories = Category::active()->get();
        
        return view('performance.activities.index', compact('activities', 'categories', 'taggedCount', 'untaggedCount'));
    }

    public function tagged()
    {
        $activities = Activity::tagged()
            ->with('categories')
            ->orderBy('start_time_utc', 'desc')
            ->limit(1000)
            ->get();
        $categories = Category::active()->get();
        
        return view('performance.activities.tagged', compact('activities', 'categories'));
    }

    public function untagged()
    {
        $activities = Activity::untagged()
            ->orderBy('start_time_utc', 'desc')
            ->limit(1000)
            ->get();
        $categories = Category::active()->get();
        
        $totalCount = Activity::untagged()->count();
        
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
