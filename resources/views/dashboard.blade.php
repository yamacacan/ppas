@extends('layouts.master')

@section('title', 'Performance Dashboard')

@section('css')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Performance Dashboard</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Performans metrikleri ve analiz raporları</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Dashboard</span>
    </li>
@endsection

@section('content')
<!-- Today's Summary Bar -->
<!-- Today's Summary Bar -->
<div class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
        <!-- Total -->
        <div class="p-6 flex items-center justify-center gap-5">
             <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center transition-transform hover:scale-110 duration-200">
                <i class="fas fa-clock text-xl"></i>
             </div>
             <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Bugün Toplam</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    <span id="counter-today-total">{{ $todayStats['total'] }}</span><span class="text-sm font-medium text-gray-400 ml-1">saat</span>
                </p>
             </div>
        </div>
        
        <!-- Work -->
        <div class="p-6 flex items-center justify-center gap-5">
             <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center transition-transform hover:scale-110 duration-200">
                <i class="fas fa-check-circle text-xl"></i>
             </div>
             <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">İş (Verimli)</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    <span id="counter-today-work">{{ $todayStats['work'] }}</span><span class="text-sm font-medium text-gray-400 ml-1">saat</span>
                </p>
             </div>
        </div>
        
        <!-- Activity Count -->
        <div class="p-6 flex items-center justify-center gap-5">
             <div class="w-14 h-14 rounded-2xl bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 flex items-center justify-center transition-transform hover:scale-110 duration-200">
                <i class="fas fa-wave-square text-xl"></i>
             </div>
             <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Aktivite</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    <span id="counter-today-activities">{{ number_format($todayStats['activities']) }}</span>
                </p>
             </div>
        </div>
        
        <!-- Tagging Rate -->
        <div class="p-6 flex items-center justify-center gap-5">
             <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 flex items-center justify-center transition-transform hover:scale-110 duration-200">
                <i class="fas fa-percentage text-xl"></i>
             </div>
             <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Tagleme</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    %<span id="counter-tagging-rate">{{ $taggingRate }}</span>
                </p>
             </div>
        </div>
    </div>
</div>

<!-- Main Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- Total Hours -->
    <div class="card stat-card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Toplam Süre</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                        <span id="counter-total-hours">{{ number_format($totalHours, 1) }}</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Son 30 Gün</p>
                </div>
                <div class="stat-icon bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Hours -->
    <div class="card stat-card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">İş Aktiviteleri</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                        <span id="counter-work-hours">{{ number_format($workHours, 1) }}</span>
                    </h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-semibold">saat (Son 30 Gün)</p>
                </div>
                <div class="stat-icon bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Other Hours -->
    <div class="card stat-card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Diğer Aktiviteler</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                        <span id="counter-other-hours">{{ number_format($otherHours, 1) }}</span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">saat (Son 30 Gün)</p>
                </div>
                <div class="stat-icon bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <i class="fas fa-coffee text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Untagged Hours -->
    <div class="card stat-card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tanımsız Süre</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                        <span id="counter-untagged-hours">{{ number_format($untaggedHours, 1) }}</span>
                    </h3>
                    <p class="text-xs text-red-500 mt-1">saat (Son 30 Gün)</p>
                </div>
                <div class="stat-icon bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <i class="fas fa-question-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- 7-Day Multi-Line Trend -->
    <div class="xl:col-span-2 card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-area text-primary-500"></i>
                        7 Günlük Performans Trendi
                    </h5>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Toplam, Taglenmiş ve Tanımsız karşılaştırması</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <span class="text-xs flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <span class="w-3 h-0.5 bg-blue-500"></span> Toplam
                    </span>
                    <span class="text-xs flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <span class="w-3 h-0.5 bg-green-500"></span> İş (Work)
                    </span>
                    <span class="text-xs flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <span class="w-3 h-0.5 bg-purple-500"></span> Diğer (Other)
                    </span>
                    <span class="text-xs flex items-center gap-1 text-gray-600 dark:text-gray-400">
                        <span class="w-3 h-0.5 bg-red-500"></span> Tanımsız
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="h-80">
                <canvas id="multiLineTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Work/Other Distribution -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-pie-chart text-primary-500"></i>
                İş / Diğer Dağılımı (30 Gün)
            </h5>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Son 30 günlük ortalama</p>
        </div>
        <div class="card-body">
            <div class="h-56">
                <canvas id="workOtherChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded flex items-center justify-center text-white">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">İş Aktiviteleri</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Toplam süre</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $workOtherRatio30['work']['duration_hours'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">saat</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-500 rounded flex items-center justify-center text-white">
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Diğer Aktiviteler</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Toplam süre</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-gray-600 dark:text-gray-400">{{ $workOtherRatio30['other']['duration_hours'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">saat</p>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-500 rounded flex items-center justify-center text-white">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanımsız Aktiviteler</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Toplam süre</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $workOtherRatio30['untagged']['duration_hours'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500">saat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hourly & Categories -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Hourly Distribution -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-clock text-primary-500"></i>
                Saatlik İş Dağılımı (30 Günlük)
            </h5>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tüm kullanıcıların 30 günlük ortalama günlük aktivite dağılımı</p>
        </div>
        <div class="card-body">
            <div class="h-72">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Categories -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-award text-primary-500"></i>
                En Çok Kullanılan Kategoriler
            </h5>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Top 8 kategori sıralaması</p>
        </div>
        <div class="card-body">
            <div class="h-72">
                <canvas id="categoriesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Shortcuts -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-500"></i>
            Hızlı İşlemler
        </h5>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="{{ route('kullanicilar.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 group">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı Ekle</span>
            </a>

            <a href="{{ route('keywords.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 group">
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-key text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Keyword Ekle</span>
            </a>

            <a href="{{ route('activities.untagged') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 group">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-tag text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taglenmemiş</span>
            </a>
            
             <a href="{{ route('statistics.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 group">
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Raporlar</span>
            </a>

             <a href="{{ route('firm-settings.edit') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 group">
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-cog text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ayarlar</span>
            </a>

            <a href="{{ route('computer-users.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition border border-gray-200 dark:border-gray-700 group">
                <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-desktop text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bilgisayarlar</span>
            </a>
        </div>
    </div>
</div>

<!-- Keywords & Processes -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Top Keywords -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-key text-primary-500"></i>
                En Popüler Keywordlar
            </h5>
        </div>
        <div class="card-body max-h-96 overflow-y-auto scrollbar-thin">
            <div class="space-y-2">
                @forelse($topKeywords as $index => $keyword)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0 w-8 h-8 bg-primary-500 rounded flex items-center justify-center text-white text-sm font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white truncate text-sm">{{ $keyword->keyword }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $keyword->match_type }}</span>
                            @if($keyword->category)
                                <span class="badge badge-primary text-xs">{{ $keyword->category->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $keyword->match_count }}</p>
                        <p class="text-xs text-gray-500">eşleşme</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Keyword verisi bulunamadı</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Processes -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-cogs text-primary-500"></i>
                En Çok Kullanılan Uygulamalar
            </h5>
        </div>
        <div class="card-body max-h-96 overflow-y-auto scrollbar-thin">
            <div class="space-y-2">
                @forelse($topProcesses as $index => $process)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors border border-gray-200 dark:border-gray-700">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-sm font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white truncate text-sm">{{ $process->process_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($process->activity_count) }} aktivite</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $process->total_hours }}</p>
                        <p class="text-xs text-gray-500">saat</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">Process verisi bulunamadı</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- 30-Day Work Trend -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-calendar-alt text-primary-500"></i>
            30 Günlük İş Performans Trendi
        </h5>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">İş kategorisindeki aylık aktivite özeti ve trend analizi</p>
    </div>
    <div class="card-body">
        <div class="h-64">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.umd.min.js"></script>
<script>
    // --- CountUp.js Initialization ---
    document.addEventListener('DOMContentLoaded', function() {
        const options = { duration: 2.5, useEasing: true, useGrouping: true };
        
        // Helper to init countUp safely
        const initCounter = (id, decimalPlaces = 0) => {
            const el = document.getElementById(id);
            if(el) {
                const val = parseFloat(el.innerText.replace(',', '.').replace(/[^0-9.-]/g, '')); // Clean val
                const anim = new countUp.CountUp(id, val, { ...options, decimalPlaces });
                if (!anim.error) anim.start();
            }
        };

        // Delay slightly for visual effect
        setTimeout(() => {
            initCounter('counter-today-total', 1);
            initCounter('counter-today-work', 1);
            initCounter('counter-today-activities', 0);
            initCounter('counter-tagging-rate', 1);
            
            initCounter('counter-total-hours', 1);
            initCounter('counter-work-hours', 1);
            initCounter('counter-other-hours', 1);
            initCounter('counter-untagged-hours', 1);
        }, 300);
    });

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(75, 85, 99, 0.2)' : 'rgba(229, 231, 235, 0.5)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = textColor;

    // Multi-Line Trend Chart
    new Chart(document.getElementById('multiLineTrendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($last7Days, 'date')) !!},
            datasets: [
                {
                    label: 'Toplam',
                    data: {!! json_encode(array_column($last7Days, 'count')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'İş (Work)',
                    data: {!! json_encode($last7DaysWork) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.05)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Diğer (Other)',
                    data: {!! json_encode($last7DaysOther) !!},
                    borderColor: 'rgb(168, 85, 247)', // Purple
                    backgroundColor: 'rgba(168, 85, 247, 0.05)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Tanımsız',
                    data: {!! json_encode($last7DaysUntagged) !!},
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor }
                }
            }
        }
    });

    // Work/Other Chart
    new Chart(document.getElementById('workOtherChart'), {
        type: 'doughnut',
        data: {
            labels: ['İş', 'Diğer', 'Tanımsız'],
            datasets: [{
                data: [
                    {{ $workOtherRatio30['work']['duration_hours'] ?? 0 }},
                    {{ $workOtherRatio30['other']['duration_hours'] ?? 0 }},
                    {{ $workOtherRatio30['untagged']['duration_hours'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(107, 114, 128)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            }
        }
    });

    // Hourly Distribution
    new Chart(document.getElementById('hourlyChart'), {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => i + ':00'),
            datasets: [{
                label: 'Süre (Saat)',
                data: {!! json_encode($hourlyDistribution) !!},
                backgroundColor: 'rgb(139, 92, 246)',
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, maxRotation: 0 }
                }
            }
        }
    });

    // Top Categories
    const categoryColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];
    new Chart(document.getElementById('categoriesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topCategories->pluck('name')->toArray()) !!},
            datasets: [{
                label: 'Süre (Saat)',
                data: {!! json_encode($topCategories->pluck('total_duration_hours')->toArray()) !!},
                backgroundColor: categoryColors,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: textColor }
                }
            }
        }
    });

    // Monthly Trend
    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($last30Days, 'date')) !!},
            datasets: [{
                label: 'Süre (Saat)',
                data: {!! json_encode(array_column($last30Days, 'count')) !!},
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.05)',
                borderWidth: 2,
                tension: 0.1,
                fill: true,
                pointRadius: 2,
                pointHoverRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: textColor,
                        maxRotation: 45,
                        callback: function(value, index) {
                            return index % 3 === 0 ? this.getLabelForValue(value) : '';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
