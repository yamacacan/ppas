<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Category;
use App\Models\ActivitySummary;
use App\Models\ProcessSummary;
use App\Models\KeywordSummary;

class SyncActivitySummaries extends Command
{
    protected $signature = 'activities:sync-summaries {--days=30} {--all}';
    protected $description = 'Sync activity statistics to the summary table for faster dashboard loading';

    public function handle()
    {
        $days = $this->option('all') ? 365 * 10 : (int)$this->option('days');
        $startDate = now()->subDays($days)->startOfDay();
        
        $this->info("Syncing activity summaries starting from: " . $startDate->toDateString());

        // 1. Kategorileri tiplerine göre gruplayalım
        $workCategoryIds = Category::where('type', 'work')->pluck('id')->toArray();
        $otherCategoryIds = Category::where('type', 'other')->pluck('id')->toArray();

        // Hedef günleri tek tek işleyelim (büyük veride hafıza yönetimi için)
        for ($i = 0; $i <= $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $this->comment("Processing date: $date");

            // Önce o güne ait eski özetleri temizleyelim
            ActivitySummary::where('date', $date)->delete();
            ProcessSummary::where('date', $date)->delete();
            KeywordSummary::where('date', $date)->delete();

            // A. TİP BAZLI ÖZETLER (İş, Diğer, Tanımsız - Tekilleştirilmiş)
            
            // i. Work
            $this->syncTypeStats($date, 'work', $workCategoryIds);
            
            // ii. Other
            $this->syncTypeStats($date, 'other', $otherCategoryIds);
            
            // iii. Untagged
            $this->syncUntaggedStats($date);

            // B. KATEGORİ BAZLI ÖZETLER (Her kategori için ayrı)
            $this->syncCategorySpecificStats($date);

            // C. PROCESS BAZLI ÖZETLER
            $this->syncProcessStats($date);

            // D. KEYWORD BAZLI ÖZETLER
            $this->syncKeywordStats($date);
        }

        $this->info("Sync completed successfully!");
    }

    private function syncTypeStats($date, $type, $categoryIds)
    {
        if (empty($categoryIds)) return;

        $stats = DB::table('activities as a')
            ->select(
                'a.username',
                'a.motherboard_uuid',
                DB::raw('HOUR(a.start_time_utc) as hour'),
                DB::raw('SUM(a.duration_ms) as total_duration'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->whereDate('a.start_time_utc', $date)
            ->whereExists(function ($query) use ($categoryIds) {
                $query->select(DB::raw(1))
                    ->from('activity_categories as ac')
                    ->whereColumn('ac.activity_id', 'a.id')
                    ->whereIn('ac.category_id', $categoryIds);
            })
            ->groupBy('a.username', 'a.motherboard_uuid', DB::raw('HOUR(a.start_time_utc)'))
            ->get();

        foreach ($stats as $stat) {
            ActivitySummary::create([
                'date' => $date,
                'hour' => $stat->hour,
                'username' => $stat->username,
                'motherboard_uuid' => $stat->motherboard_uuid,
                'category_type' => $type,
                'category_id' => null,
                'total_duration_ms' => $stat->total_duration,
                'activity_count' => $stat->activity_count,
            ]);
        }
    }

    private function syncUntaggedStats($date)
    {
        $stats = DB::table('activities as a')
            ->select(
                'a.username',
                'a.motherboard_uuid',
                DB::raw('HOUR(a.start_time_utc) as hour'),
                DB::raw('SUM(a.duration_ms) as total_duration'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->whereDate('a.start_time_utc', $date)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('activity_categories as ac')
                    ->whereColumn('ac.activity_id', 'a.id');
            })
            ->groupBy('a.username', 'a.motherboard_uuid', DB::raw('HOUR(a.start_time_utc)'))
            ->get();

        foreach ($stats as $stat) {
            ActivitySummary::create([
                'date' => $date,
                'hour' => $stat->hour,
                'username' => $stat->username,
                'motherboard_uuid' => $stat->motherboard_uuid,
                'category_type' => 'untagged',
                'category_id' => null,
                'total_duration_ms' => $stat->total_duration,
                'activity_count' => $stat->activity_count,
            ]);
        }
    }

    private function syncCategorySpecificStats($date)
    {
        $stats = DB::table('activities as a')
            ->join('activity_categories as ac', 'a.id', '=', 'ac.activity_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->select(
                'a.username',
                'a.motherboard_uuid',
                DB::raw('HOUR(a.start_time_utc) as hour'),
                'ac.category_id',
                'c.type as category_type',
                'ac.is_manual',
                DB::raw('SUM(a.duration_ms) as total_duration'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->whereDate('a.start_time_utc', $date)
            ->groupBy('a.username', 'a.motherboard_uuid', DB::raw('HOUR(a.start_time_utc)'), 'ac.category_id', 'c.type', 'ac.is_manual')
            ->get();

        foreach ($stats as $stat) {
            ActivitySummary::create([
                'date' => $date,
                'hour' => $stat->hour,
                'username' => $stat->username,
                'motherboard_uuid' => $stat->motherboard_uuid,
                'category_type' => $stat->category_type,
                'category_id' => $stat->category_id,
                'is_manual' => $stat->is_manual,
                'total_duration_ms' => $stat->total_duration,
                'activity_count' => $stat->activity_count,
            ]);
        }
    }
    private function syncProcessStats($date)
    {
        $stats = DB::table('activities')
            ->select(
                'username',
                'motherboard_uuid',
                'process_name',
                DB::raw('SUM(duration_ms) as total_duration'),
                DB::raw('COUNT(*) as activity_count')
            )
            ->whereDate('start_time_utc', $date)
            ->groupBy('username', 'motherboard_uuid', 'process_name')
            ->get();

        foreach ($stats as $stat) {
            ProcessSummary::create([
                'date' => $date,
                'username' => $stat->username,
                'motherboard_uuid' => $stat->motherboard_uuid,
                'process_name' => $stat->process_name,
                'total_duration_ms' => $stat->total_duration,
                'activity_count' => $stat->activity_count,
            ]);
        }
    }

    private function syncKeywordStats($date)
    {
        $stats = DB::table('activity_categories as ac')
            ->join('activities as a', 'ac.activity_id', '=', 'a.id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->select(
                'a.username',
                'a.motherboard_uuid',
                'ac.matched_keyword as keyword',
                'ac.category_id',
                'c.name as category_name',
                DB::raw('COUNT(*) as match_count'),
                DB::raw('SUM(a.duration_ms) as total_duration')
            )
            ->whereDate('ac.tagged_at', $date)
            ->whereNotNull('ac.matched_keyword')
            ->where('ac.matched_keyword', '!=', '')
            ->groupBy('a.username', 'a.motherboard_uuid', 'ac.matched_keyword', 'ac.category_id', 'c.name')
            ->get();

        foreach ($stats as $stat) {
            KeywordSummary::create([
                'date' => $date,
                'username' => $stat->username,
                'motherboard_uuid' => $stat->motherboard_uuid,
                'keyword' => $stat->keyword,
                'category_id' => $stat->category_id,
                'category_name' => $stat->category_name,
                'match_count' => $stat->match_count,
                'total_duration_ms' => $stat->total_duration,
            ]);
        }
    }
}
