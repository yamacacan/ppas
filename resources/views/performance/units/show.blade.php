@extends('layouts.master')

@section('title', $unit->name . ' - Detaylı Performans Analizi')

@section('css')
<style>
    /* Modern Card Effects */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.15);
    }
    
    /* Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 20px;
    }
    
    /* Chart Tooltip Customization */
    .apexcharts-tooltip {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }
</style>
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">{{ $unit->name }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
            Performans Raporu ve Detaylı Analizler
        </p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('unit-statistics.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 transition-colors">Birimler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-900 dark:text-white font-medium">Detaylar</span>
    </li>
@endsection

@section('content')

<!-- Header & Actions -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <!-- Date Range Picker (Visual) -->
    <div class="flex items-center bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-1">
        <button class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
            Bugün
        </button>
        <button class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
            Bu Hafta
        </button>
        <button class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg border border-blue-100 dark:border-blue-800 transition-colors shadow-sm">
            Bu Ay
        </button>
    </div>

    <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all shadow-sm group">
            <i class="fas fa-download text-gray-400 group-hover:text-blue-500 transition-colors"></i>
            <span class="text-sm font-medium">PDF Rapor</span>
        </button>
        <button class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-share-alt"></i>
            <span class="text-sm font-medium">Paylaş</span>
        </button>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Aktif Personel -->
    <div class="stat-card bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 dark:bg-blue-900/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
        
        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktif Personel</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $unit->computer_users_count }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        
        <div class="flex items-center gap-2 relative z-10">
            <div class="flex -space-x-2"> 
                @foreach($computerUsers->take(4) as $user)
                <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-gray-600">
                    {{ substr($user->user_name, 0, 1) }}
                </div>
                @endforeach
                @if($unit->computer_users_count > 4)
                <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white dark:border-gray-800 flex items-center justify-center text-xs font-bold text-gray-500">
                    +{{ $unit->computer_users_count - 4 }}
                </div>
                @endif
            </div>
            <span class="text-xs text-gray-500 ml-auto">Birim Mevcudu</span>
        </div>
    </div>

    <!-- Toplam Efor -->
    <div class="stat-card bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 dark:bg-green-900/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Efor</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($workOtherRatio['total']['duration_hours']) }}<span class="text-lg text-gray-400 ml-1 font-normal">h</span></h3>
            </div>
            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400">
                <i class="fas fa-bolt text-xl"></i>
            </div>
        </div>
        
        <div class="flex items-center gap-2 text-sm mt-auto relative z-10">
            <span class="inline-flex items-center text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-lg font-medium text-xs">
                <i class="fas fa-arrow-up mr-1"></i> %12
            </span>
            <span class="text-gray-400">geçen haftaya göre</span>
        </div>
    </div>

    <!-- Verimlilik Skoru -->
    <div class="stat-card bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 dark:bg-purple-900/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Verimlilik</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">%{{ number_format($workOtherRatio['work']['percentage'], 0) }}</h3>
            </div>
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-purple-600 dark:text-purple-400">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
        </div>
        
        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 mt-auto relative z-10">
            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $workOtherRatio['work']['percentage'] }}%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Birim genel ortalaması</p>
    </div>

    <!-- Tagleme Oranı -->
    <div class="stat-card bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 dark:bg-orange-900/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>

        <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktif Projeler</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">8</h3>
            </div>
            <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400">
                <i class="fas fa-project-diagram text-xl"></i>
            </div>
        </div>
        
        <div class="flex items-center gap-2 mt-auto relative z-10">
            <span class="text-sm font-medium text-gray-900 dark:text-white">5 tamamlandı</span>
            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
            <span class="text-sm text-gray-500">3 devam ediyor</span>
        </div>
    </div>
</div>

<!-- Main Charts Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    
    <!-- Weekly Rhythm (Large) -->
    <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg">Haftalık Çalışma Ritmi</h3>
                <p class="text-sm text-gray-500 mt-1">Son 7 günlük aktivite yoğunluğu</p>
            </div>
            <div class="flex items-center gap-2">
                 <span class="flex items-center gap-1.5 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Bu Hafta</span>
                </span>
            </div>
        </div>
        <div id="weeklyRhythmChart" class="w-full"></div>
    </div>

    <!-- Performance Summary (Radial) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
        <div class="mb-4">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Performans Özeti</h3>
            <p class="text-sm text-gray-500 mt-1">Genel başarı göstergeleri</p>
        </div>
        
        <div class="flex-1 flex items-center justify-center">
            <div id="performanceRadialChart"></div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
             <div class="text-center">
                <p class="text-xs text-gray-500 mb-1">İş Verimi</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">%{{ number_format($workOtherRatio['work']['percentage'], 0) }}</p>
            </div>
            <div class="text-center border-l border-gray-100 dark:border-gray-700">
                <p class="text-xs text-gray-500 mb-1">Mesai Uyumu</p>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format(($workingHourStats['working_hours']['work'] / max(1, $workOtherRatio['total']['duration_hours'])) * 100, 0) }}%
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analysis Row -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
    
    <!-- Work vs Other -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg">İş vs Diğer Aktivite</h3>
            </div>
            <!-- Custom Legend -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-sm bg-blue-600"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">İş</span>
                </div>
                <div class="flex items-center gap-2">
                     <span class="w-2.5 h-2.5 rounded-sm bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Diğer</span>
                </div>
            </div>
        </div>
        <div id="workOtherChart"></div>
    </div>

    <!-- Working Hours -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg">Mesai Analizi</h3>
            </div>
            <!-- Custom Legend -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-sm bg-blue-600"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Mesai İçi</span>
                </div>
                <div class="flex items-center gap-2">
                     <span class="w-2.5 h-2.5 rounded-sm bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Mesai Dışı</span>
                </div>
            </div>
        </div>
        <div id="workingHoursChart"></div>
    </div>
</div>

<!-- Bottom Row: Top Categories, Keywords & Processes -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Top Categories -->
     <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
         <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-4">Kategori Dağılımı</h3>
         <div id="categoryChart" class="flex justify-center"></div>
     </div>

     <!-- Top Keywords -->
     <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-4">Popüler Aramalar</h3>
        <div class="space-y-3 custom-scrollbar max-h-[300px] overflow-y-auto pr-2">
            @forelse($topKeywords->take(6) as $index => $keyword)
            <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-white dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all group">
                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm mr-3">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $keyword->keyword }}</p>
                    <p class="text-xs text-gray-500">{{ $keyword->match_count }} eşleşme</p>
                </div>
                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">Veri bulunamadı</div>
            @endforelse
        </div>
     </div>

     <!-- Top Processes -->
     <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-4">Popüler Uygulamalar</h3>
        <div class="space-y-3 custom-scrollbar max-h-[300px] overflow-y-auto pr-2">
            @forelse($topProcesses->take(6) as $index => $process)
            <div class="flex items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 hover:bg-white dark:hover:bg-gray-700 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all group">
                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center font-bold text-sm mr-3">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $process->process_name }}</p>
                    <p class="text-xs text-gray-500">{{ $process->total_hours }} saat</p>
                </div>
                <div class="w-1.5 h-1.5 rounded-full bg-green-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">Veri bulunamadı</div>
            @endforelse
        </div>
     </div>
</div>

<!-- Computer Users Table -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Personel Listesi</h3>
            <p class="text-sm text-gray-500 mt-1">Birim personellerinin performans detayları</p>
        </div>
        <div class="flex gap-2">
            <input type="text" placeholder="Personel ara..." class="px-4 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 font-semibold tracking-wider border-b border-gray-200 dark:border-gray-700">
                    <th class="px-6 py-4">Personel</th>
                    <th class="px-6 py-4">Toplam Süre</th>
                    <th class="px-6 py-4">Son Aktivite</th>
                    <th class="px-6 py-4 text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($computerUsers as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900 dark:to-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold border border-blue-200 dark:border-blue-700">
                                {{ substr($user->user_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $user->user_name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->computer_name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-700 dark:text-gray-200">{{ number_format($user->total_duration_hours, 1) }}h</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500">{{ $user->last_activity ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                         <a href="{{ route('computer-users.show', $user->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all shadow-sm">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Shared Chart Config
    const commonConfig = {
        fontFamily: "'Inter', sans-serif",
        foreColor: isDarkMode ? '#9ca3af' : '#6b7280',
        toolbar: { show: false },
        zoom: { enabled: false }
    };

    const getGridColor = () => isDarkMode ? '#374151' : '#f3f4f6';
    const getTextColor = () => isDarkMode ? '#e5e7eb' : '#1f2937';

    // 1. Weekly Rhythm Chart
    const weeklyData = {!! json_encode(array_values($weeklyRhythm)) !!};
    const weeklyLabels = {!! json_encode(array_keys($weeklyRhythm)) !!};
    
    new ApexCharts(document.querySelector("#weeklyRhythmChart"), {
        series: [{
            name: 'Çalışma Süresi',
            data: weeklyData
        }],
        chart: {
            type: 'bar',
            height: 320,
            ...commonConfig
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '50%',
                colors: {
                    ranges: [{
                        from: 0,
                        to: 1000,
                        color: '#2563eb' // Blue-600
                    }]
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: weeklyLabels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: isDarkMode ? '#9ca3af' : '#6b7280',
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: (val) => val.toFixed(1) + 'h',
                style: {
                    colors: isDarkMode ? '#9ca3af' : '#6b7280'
                }
            }
        },
        grid: {
            borderColor: getGridColor(),
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        },
        tooltip: {
            theme: isDarkMode ? 'dark' : 'light',
            y: {
                formatter: (val) => val.toFixed(1) + ' Saat'
            }
        }
    }).render();

    // 2. Performance Radial Chart (Flowbite Style)
    const workEfficiency = {{ $workOtherRatio['work']['percentage'] ?? 0 }};
    const workHoursCompliance = {{ number_format(($workingHourStats['working_hours']['work'] / max(1, $workOtherRatio['total']['duration_hours'])) * 100, 2) }};
    const overallProductivity = (workEfficiency + workHoursCompliance) / 2;
    
    new ApexCharts(document.querySelector("#performanceRadialChart"), {
        series: [workEfficiency, workHoursCompliance, overallProductivity],
        chart: {
            height: 300,
            type: 'radialBar',
            ...commonConfig
        },
        plotOptions: {
            radialBar: {
                track: {
                    background: isDarkMode ? '#374151' : '#f3f4f6',
                    strokeWidth: '100%',
                    margin: 5
                },
                dataLabels: {
                    total: {
                        show: true,
                        label: 'Skor',
                        formatter: function (w) {
                             return Math.round(overallProductivity) + '%';
                        }
                    },
                    value: {
                         fontSize: '24px',
                         fontWeight: 700,
                         color: getTextColor()
                    }
                },
                hollow: {
                    margin: 15,
                    size: '45%'
                }
            }
        },
        colors: ['#10b981', '#3b82f6', '#8b5cf6'], // Green, Blue, Purple
        labels: ['İş Verimi', 'Mesai Uyumu', 'Genel Başarı'],
        stroke: { lineCap: 'round' },
        legend: {
            show: true,
            position: 'bottom',
            fontSize: '12px',
            itemMargin: { horizontal: 5 }
        }
    }).render();

    // 3. Work vs Other
    new ApexCharts(document.querySelector("#workOtherChart"), {
        series: [{
            name: 'İş',
            data: [{{ $workOtherRatio['work']['duration_hours'] }}]
        }, {
            name: 'Diğer',
            data: [{{ $workOtherRatio['other']['duration_hours'] }}]
        }],
        chart: {
            type: 'bar',
            height: 250,
            stacked: false,
            ...commonConfig
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '50%',
                horizontal: true,
                barHeight: '40%'
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
             categories: ['Süre'],
             labels: {
                 formatter: (val) => val.toFixed(0) + 'h'
             }
        },
        colors: ['#2563eb', isDarkMode ? '#4b5563' : '#d1d5db'], // Blue, Gray
        legend: { show: false }, // Using custom HTML legend
        grid: {
            borderColor: getGridColor(),
            strokeDashArray: 4
        },
        tooltip: {
            theme: isDarkMode ? 'dark' : 'light',
            y: { formatter: (val) => val + ' Saat' }
        }
    }).render();

    // 4. Working Hours
    new ApexCharts(document.querySelector("#workingHoursChart"), {
        series: [{
            name: 'İş (Work)',
            data: [
                {{ $workingHourStats['working_hours']['work'] ?? 0 }},
                {{ $workingHourStats['outside_hours']['work'] ?? 0 }}
            ]
        }, {
            name: 'Diğer (Other)',
            data: [
                {{ $workingHourStats['working_hours']['other'] ?? 0 }},
                {{ $workingHourStats['outside_hours']['other'] ?? 0 }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 250,
            stacked: false,
            ...commonConfig
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '55%'
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ['Mesai İçi', 'Mesai Dışı'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
         colors: ['#2563eb', isDarkMode ? '#4b5563' : '#d1d5db'], // Blue, Gray
        legend: { show: false }, // Using custom HTML legend
        grid: {
            borderColor: getGridColor(),
            strokeDashArray: 4
        },
        tooltip: {
            theme: isDarkMode ? 'dark' : 'light'
        }
    }).render();

    // 5. Category Distribution (Simple Donut)
    new ApexCharts(document.querySelector("#categoryChart"), {
        series: {!! json_encode($topCategories->pluck('total_duration_hours')->toArray()) !!},
        labels: {!! json_encode($topCategories->pluck('name')->toArray()) !!},
        chart: {
            type: 'donut',
            height: 300,
            ...commonConfig
        },
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899'],
        legend: {
            position: 'bottom',
            fontSize: '12px'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Toplam',
                            color: getTextColor(),
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toFixed(0) + 'h';
                            }
                        }
                    }
                }
            }
        },
        stroke: { show: false }
    }).render();

</script>
@endsection
