@extends('layouts.master')

@section('title', 'Birim Detayları: ' . $unit->name)

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/chart/apex-chart/apex-chart.css') }}">
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $unit->name }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Birim performans analizi ve istatistikleri</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('unit-statistics.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary-600">Birimler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Detay</span>
    </li>
@endsection

@section('content')

<!-- Filtreler -->
@include('performance.computer_users.partials.filter', ['route' => route('unit-statistics.show', $unit->id), 'filters' => $filters])

<!-- İstatistik Kartları - Büyütülmüş ve Daha Detaylı -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <!-- Toplam Süre -->
    <div class="card relative overflow-hidden group hover:shadow-2xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-50 to-transparent dark:from-primary-900/10 opacity-50"></div>
        <div class="card-body relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-medium">Toplam Çalışma</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $workOtherRatio['total']['duration_hours'] }}
                    </h3>
                    <p class="text-xs text-gray-500">saat aktivite</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">{{ $unit->computer_users_count }} personel</span>
                    <i class="fas fa-arrow-trend-up text-primary-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Verimlilik -->
    <div class="card relative overflow-hidden group hover:shadow-2xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-transparent dark:from-green-900/10 opacity-50"></div>
        <div class="card-body relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-medium">Verim Oranı</p>
                    <h3 class="text-3xl font-bold text-green-600 dark:text-green-400 mb-1">
                        %{{ $workOtherRatio['work']['percentage'] }}
                    </h3>
                    <p class="text-xs text-gray-500">{{ $workOtherRatio['work']['duration_hours'] }} saat iş</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $workOtherRatio['work']['percentage'] }}%"></div>
            </div>
        </div>
    </div>

    <!-- Personel Sayı -->
    <div class="card relative overflow-hidden group hover:shadow-2xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent dark:from-blue-900/10 opacity-50"></div>
        <div class="card-body relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-medium">Personel</p>
                    <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">{{ $unit->computer_users_count }}</h3>
                    <p class="text-xs text-gray-500">toplam kullanıcı</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Aktif çalışan</span>
                    <i class="fas fa-check-circle text-blue-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mesai Dışı -->
    <div class="card relative overflow-hidden group hover:shadow-2xl transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-transparent dark:from-orange-900/10 opacity-50"></div>
        <div class="card-body relative">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-medium">Mesai Dışı</p>
                    <h3 class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-1">
                        {{ $workingHourStats['outside_hours']['work'] }}
                    </h3>
                    <p class="text-xs text-gray-500">saat iş aktivitesi</p>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-moon text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">Fazla mesai</span>
                    <i class="fas fa-exclamation-triangle text-orange-500"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Haftalık Ritim - TAM GENİŞLİK -->
<div class="card hover:shadow-xl transition-shadow mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-50 to-transparent dark:from-primary-900/20">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">Haftalık Çalışma Ritmi</h5>
                    <p class="text-xs text-gray-500">Günlük ortalama aktivite süresi</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="weeklyRhythmChart"></div>
    </div>
</div>

<!-- Kategori ve Performans Metrikleri -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Kategori Dağılımı -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pie-chart text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">Kategori Dağılımı</h5>
                    <p class="text-xs text-gray-500">İş/diğer aktiviteler</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="categoryChart"></div>
        </div>
    </div>

    <!-- Performans Metrikleri - YENİ RADIAL CHART -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-transparent dark:from-indigo-900/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">Performans Metrikleri</h5>
                    <p class="text-xs text-gray-500">Birim başarı göstergeleri</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg flex flex-col items-center justify-center p-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 text-sm font-bold flex items-center justify-center mb-2">
                        {{ number_format($workOtherRatio['work']['percentage'], 0) }}%
                    </div>
                    <p class="text-xs font-medium text-green-700 dark:text-green-300 text-center">İş Oranı</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg flex flex-col items-center justify-center p-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-sm font-bold flex items-center justify-center mb-2">
                        {{ number_format(($workingHourStats['working_hours']['work'] / max(1, $workOtherRatio['total']['duration_hours'])) * 100, 0) }}%
                    </div>
                    <p class="text-xs font-medium text-blue-700 dark:text-blue-300 text-center">Mesai İçi</p>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg flex flex-col items-center justify-center p-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 text-sm font-bold flex items-center justify-center mb-2">
                        {{ $unit->computer_users_count }}
                    </div>
                    <p class="text-xs font-medium text-purple-700 dark:text-purple-300 text-center">Personel</p>
                </div>
            </div>

            <!-- Radial Chart -->
            <div id="performanceRadialChart"></div>

            <!-- Additional Details -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Ortalama Verimlilik:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        {{ number_format($workOtherRatio['work']['percentage'], 1) }}%
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Aktif Çalışan:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $unit->computer_users_count }} kişi
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Toplam Çalışma:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ $workOtherRatio['total']['duration_hours'] }} saat
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- İş vs Diğer Oranı Grafik -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-transparent p-6">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-lg">
                İş vs Diğer Aktivite
            </h5>
            <p class="text-sm text-gray-500 mt-1">Süre bazlı karşılaştırma</p>
        </div>
        <div class="card-body p-6">
            <!-- Custom Legend -->
            <div class="flex justify-center sm:justify-end items-center gap-x-4 mb-3 sm:mb-6">
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-blue-600 rounded-sm me-2"></span>
                    <span class="text-[13px] text-gray-600 dark:text-gray-400">
                        İş Aktiviteleri
                    </span>
                </div>
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-gray-300 rounded-sm me-2 dark:bg-gray-600"></span>
                    <span class="text-[13px] text-gray-600 dark:text-gray-400">
                        Diğer Aktiviteler
                    </span>
                </div>
            </div>
            <div id="workOtherChart"></div>
        </div>
    </div>
</div>

<!-- Mesai Saatleri - TAM GENİŞLİK -->
<div class="card hover:shadow-xl transition-shadow mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-transparent p-6">
        <div class="flex items-center justify-between">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white text-lg">Mesai Saatleri Dağılımı</h5>
                <p class="text-sm text-gray-500 mt-1">Mesai içi ve dışı çalışma analizi</p>
            </div>
        </div>
    </div>
    <div class="card-body p-6">
        <!-- Custom Legend -->
        <div class="flex justify-center sm:justify-end items-center gap-x-4 mb-3 sm:mb-6">
            <div class="inline-flex items-center">
                <span class="size-2.5 inline-block bg-blue-600 rounded-sm me-2"></span>
                <span class="text-[13px] text-gray-600 dark:text-gray-400">
                    İş (Work)
                </span>
            </div>
            <div class="inline-flex items-center">
                <span class="size-2.5 inline-block bg-gray-300 rounded-sm me-2 dark:bg-gray-600"></span>
                <span class="text-[13px] text-gray-600 dark:text-gray-400">
                    Diğer (Other)
                </span>
            </div>
        </div>
        <div id="workingHoursChart"></div>
    </div>
</div>

<!-- En Çok Kullanılanlar - Grafik + Tablo -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- Top Keywords - Grafik Olarak -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-transparent dark:from-indigo-900/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">En Çok Kullanılan Kelimeler</h5>
                    <p class="text-xs text-gray-500">Kullanım sıklı</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="topKeywordsChart"></div>
        </div>
    </div>

    <!-- Top Processes - Grafik Olarak -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-pink-50 to-transparent dark:from-pink-900/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cogs text-pink-600 dark:text-pink-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">En Çok Kullanılan Uygulamalar</h5>
                    <p class="text-xs text-gray-500">Süre bazlı sıralama</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="topProcessesChart"></div>
        </div>
    </div>
</div>

<!-- Kullanıcı Performans Tablosu -->
<div class="card hover:shadow-xl transition-shadow">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-cyan-50 to-transparent dark:from-cyan-900/20">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-friends text-cyan-600 dark:text-cyan-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">Birim Personeli Performans Özeti</h5>
                    <p class="text-xs text-gray-500">Detaylı kullanıcı istatistikleri</p>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table table-hover">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3">Personel</th>
                        <th class="px-6 py-3 text-center">Aktivite</th>
                        <th class="px-6 py-3 text-center">Süre (Saat)</th>
                        <th class="px-6 py-3 text-center">Verimlilik</th>
                        <th class="px-6 py-3 text-right">Detay</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach($computerUsers as $user)
                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-primary-700 dark:text-primary-300">{{ strtoupper(substr($user->name ?? $user->username, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $user->name ?? $user->username }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ number_format($user->activities_count) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-medium text-gray-900 dark:text-white">{{ number_format($user->total_duration_hours, 1) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" style="width: {{ min(100, ($user->total_duration_hours / max(1, $workOtherRatio['total']['duration_hours'])) * 100) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ number_format(min(100, ($user->total_duration_hours / max(1, $workOtherRatio['total']['duration_hours'])) * 100), 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('computer-users.show', $user->id) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 text-primary-600 hover:text-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all">
                                <i class="fas fa-external-link-alt text-xs"></i>
                                <span class="text-sm">Detay</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
<script>
    // Dark mode detection
    const isDarkMode = document.documentElement.classList.contains('dark');
    
    // Dark mode theme configuration
    const darkModeConfig = {
        theme: {
            mode: isDarkMode ? 'dark' : 'light'
        },
        chart: {
            background: 'transparent',
            foreColor: isDarkMode ? '#e5e7eb' : '#374151'
        },
        grid: {
            borderColor: isDarkMode ? '#374151' : '#e7e7e7'
        },
        xaxis: {
            labels: {
                style: {
                    colors: isDarkMode ? '#9ca3af' : '#6b7280'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: isDarkMode ? '#9ca3af' : '#6b7280'
                }
            }
        },
        tooltip: {
            theme: isDarkMode ? 'dark' : 'light'
        }
    };
    
    // Haftalık Ritim Grafiği - GELİŞTİRİLMİŞ ve DÜZELTİLMİŞ
    var weeklyData = @json($weeklyRhythm);
    var weeklyDays = [];
    var weeklyHours = [];
    
    // Veriyi düzgün çıkar
    if (Array.isArray(weeklyData)) {
        weeklyData.forEach(function(item) {
            weeklyDays.push(item.day || '');
            weeklyHours.push(parseFloat(item.avg_hours) || 0);
        });
    }
    
    var weeklyOptions = {
        ...darkModeConfig,
        chart: {
            ...darkModeConfig.chart,
            height: 400,
            type: 'bar',
            toolbar: { 
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 12,
                columnWidth: '75%',
                dataLabels: {
                    position: 'top'
                },
                distributed: false
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val > 0 ? val.toFixed(1) + "h" : '';
            },
            offsetY: -25,
            style: {
                fontSize: '12px',
                fontWeight: 'bold',
                colors: [isDarkMode ? "#ffffff" : "#1f2937"]
            },
            background: {
                enabled: false
            }
        },
        stroke: { 
            show: true, 
            width: 3, 
            colors: ['transparent'] 
        },
        series: [{
            name: 'Ortalama Çalışma (Saat)',
            data: weeklyHours
        }],
        xaxis: {
            ...darkModeConfig.xaxis,
            categories: weeklyDays,
            labels: {
                style: {
                    fontSize: '14px',
                    fontWeight: 700,
                    colors: isDarkMode ? '#e5e7eb' : '#374151'
                },
                offsetY: 5
            },
            axisBorder: {
                show: true,
                color: isDarkMode ? '#4b5563' : '#d1d5db'
            },
            axisTicks: {
                show: true,
                color: isDarkMode ? '#4b5563' : '#d1d5db'
            }
        },
        yaxis: {
            ...darkModeConfig.yaxis,
            title: { 
                text: 'Çalışma Süresi (Saat)',
                style: {
                    fontSize: '14px',
                    fontWeight: 700,
                    color: isDarkMode ? '#9ca3af' : '#6b7280'
                },
                offsetX: -10
            },
            labels: {
                formatter: function (val) {
                    return val.toFixed(1) + 'h';
                },
                style: {
                    fontSize: '13px',
                    fontWeight: 600,
                    colors: isDarkMode ? '#9ca3af' : '#6b7280'
                }
            },
            min: 0
        },
        fill: {
            type: 'solid',
            opacity: 1
        },
        colors: ['#2563eb'], // Blue-600
        grid: {
            ...darkModeConfig.grid,
            strokeDashArray: 4,
            borderColor: isDarkMode ? '#374151' : '#e5e7eb',
            row: {
                colors: [isDarkMode ? 'rgba(115, 102, 255, 0.03)' : 'rgba(115, 102, 255, 0.02)', 'transparent'],
                opacity: 0.5
            },
            column: {
                colors: [isDarkMode ? 'rgba(255,255,255,0.02)' : '#fafafa', 'transparent'],
                opacity: 0.5
            },
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            },
            padding: {
                top: 20,
                right: 20,
                bottom: 10,
                left: 10
            }
        },
        tooltip: {
            ...darkModeConfig.tooltip,
            enabled: true,
            shared: false,
            followCursor: true,
            custom: function({series, seriesIndex, dataPointIndex, w}) {
                const value = series[seriesIndex][dataPointIndex];
                const day = w.globals.labels[dataPointIndex];
                return `
                    <div class="px-4 py-3 rounded-lg" style="background: ${isDarkMode ? '#1f2937' : '#ffffff'}; border: 1px solid #e5e7eb;">
                        <div class="font-bold text-sm mb-1" style="color: ${isDarkMode ? '#e5e7eb' : '#1f2937'};">${day}</div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-sm bg-blue-600"></span>
                            <span style="color: ${isDarkMode ? '#9ca3af' : '#6b7280'}; font-size: 13px;">${value.toFixed(2)} saat</span>
                        </div>
                    </div>
                `;
            }
        },
        states: {
            hover: {
                filter: {
                    type: 'darken',
                    value: 0.15
                }
            },
            active: {
                filter: {
                    type: 'darken',
                    value: 0.25
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#weeklyRhythmChart"), weeklyOptions).render();

    // Kategori Dağılımı - GELİŞTİRİLMİŞ
    var categoryOptions = {
        ...darkModeConfig,
        series: @json($topCategories->pluck('percentage')),
        chart: {
            ...darkModeConfig.chart,
            type: 'donut',
            height: 350
        },
        labels: @json($topCategories->pluck('name')),
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }],
        colors: ['#7366ff', '#51bb25', '#f73164', '#f8d62b', '#a927f9', '#00d0ff', '#ff6384'],
        legend: {
            position: 'bottom',
            fontSize: '13px',
            labels: {
                colors: isDarkMode ? '#9ca3af' : '#6b7280'
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            color: isDarkMode ? '#e5e7eb' : '#374151'
                        },
                        value: {
                            color: isDarkMode ? '#e5e7eb' : '#374151'
                        },
                        total: {
                            showAlways: true,
                            show: true,
                            label: 'Toplam',
                            fontSize: '16px',
                            fontWeight: 600,
                            color: isDarkMode ? '#e5e7eb' : '#374151'
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%"
            },
            style: {
                colors: [isDarkMode ? '#1f2937' : '#fff']
            }
        }
    };
    new ApexCharts(document.querySelector("#categoryChart"), categoryOptions).render();

    // İş vs Diğer Grafik - YENİ MODERN
    var workOtherOptions = {
        ...darkModeConfig,
        series: [{
            name: 'İş Aktiviteleri',
            data: [{{ $workOtherRatio['work']['duration_hours'] }}]
        }, {
            name: 'Diğer Aktiviteler',
            data: [{{ $workOtherRatio['other']['duration_hours'] }}]
        }],
        chart: {
            ...darkModeConfig.chart,
            type: 'bar',
            height: 350,
            stacked: false,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false, 
                borderRadius: 4,
                columnWidth: '50%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            ...darkModeConfig.xaxis,
            categories: ['Birim Geneli'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            ...darkModeConfig.yaxis,
            title: { text: 'Saat' }
        },
        fill: {
            opacity: 1
        },
        states: {
            hover: {
                filter: {
                    type: 'darken',
                    value: 0.9
                }
            }
        },
        tooltip: {
            ...darkModeConfig.tooltip,
            y: {
                formatter: function (val) {
                    return val + " saat";
                }
            }
        },
        colors: ['#2563eb', isDarkMode ? '#4b5563' : '#d1d5db'], // Blue-600, Gray-600/300
        legend: {
            show: false // Custom legend used
        }
    };
    new ApexCharts(document.querySelector("#workOtherChart"), workOtherOptions).render();

    // Mesai Saatleri Grafik - YENİ MODERN
    var workingHoursOptions = {
        ...darkModeConfig,
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
            ...darkModeConfig.chart,
            type: 'bar',
            height: 350,
            stacked: false, // Grouped bars instead of stacked
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 4,
                columnWidth: '45%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            ...darkModeConfig.xaxis,
            categories: ['Mesai İçi', 'Mesai Dışı'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        fill: {
            opacity: 1
        },
        colors: ['#2563eb', isDarkMode ? '#4b5563' : '#d1d5db'], // Blue-600, Gray-600/300
        legend: {
            show: false // Custom legend used
        }
    };
    new ApexCharts(document.querySelector("#workingHoursChart"), workingHoursOptions).render();

    // Top Keywords - Bar Chart
    var topKeywordsData = @json($topKeywords);
    var topKeywordsOptions = {
        ...darkModeConfig,
        series: [{
            name: 'Kullanım Sayısı',
            data: topKeywordsData.slice(0, 10).map(item => item.count)
        }],
        chart: {
            ...darkModeConfig.chart,
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(0);
            },
            style: {
                colors: ['#fff']
            }
        },
        xaxis: {
            ...darkModeConfig.xaxis,
            categories: topKeywordsData.slice(0, 10).map(item => item.keyword)
        },
        colors: ['#7366ff', '#51bb25', '#f73164', '#f8d62b', '#a927f9', '#00d0ff', '#ff6384', '#36a2eb', '#cc65fe', '#ffce56'],
        legend: {
            show: false
        }
    };
    new ApexCharts(document.querySelector("#topKeywordsChart"), topKeywordsOptions).render();

    // Top Processes - Bar Chart
    var topProcessesData = @json($topProcesses);
    var topProcessesOptions = {
        ...darkModeConfig,
        series: [{
            name: 'Süre (Saat)',
            data: topProcessesData.slice(0, 10).map(item => parseFloat(item.duration_hours))
        }],
        chart: {
            ...darkModeConfig.chart,
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "h";
            },
            style: {
                colors: ['#fff']
            }
        },
        xaxis: {
            ...darkModeConfig.xaxis,
            categories: topProcessesData.slice(0, 10).map(item => item.process_name.length > 20 ? item.process_name.substring(0, 20) + '...' : item.process_name)
        },
        colors: ['#f73164', '#51bb25', '#7366ff', '#f8d62b', '#a927f9', '#00d0ff', '#ff6384', '#36a2eb', '#cc65fe', '#ffce56'],
        legend: {
            show: false
        }
    };
    new ApexCharts(document.querySelector("#topProcessesChart"), topProcessesOptions).render();

    // Performans Radial Chart - FLOWBITE STİLİ YENİ
    // Calculate performance metrics
    const workEfficiency = {{ $workOtherRatio['work']['percentage'] ?? 0 }};
    const workHoursCompliance = {{ number_format(($workingHourStats['working_hours']['work'] / max(1, $workOtherRatio['total']['duration_hours'])) * 100, 2) }};
    const overallProductivity = (workEfficiency + workHoursCompliance) / 2;

    // Get CSS variable colors for consistency (Flowbite style)
    const getBackgroundColor = () => {
        return isDarkMode ? 'rgba(55, 65, 81, 0.3)' : 'rgba(243, 244, 246, 0.8)';
    };

    const performanceRadialOptions = {
        ...darkModeConfig,
        series: [workEfficiency, workHoursCompliance, overallProductivity],
        colors: ['#10b981', '#3b82f6', '#8b5cf6'], // Green, Blue, Purple
        chart: {
            ...darkModeConfig.chart,
            height: 280,
            type: 'radialBar',
            sparkline: {
                enabled: false
            }
        },
        plotOptions: {
            radialBar: {
                track: {
                    background: getBackgroundColor(),
                    strokeWidth: '100%',
                    margin: 5
                },
                dataLabels: {
                    name: {
                        fontSize: '14px',
                        fontWeight: 600,
                        color: isDarkMode ? '#9ca3af' : '#6b7280',
                        offsetY: -10
                    },
                    value: {
                        fontSize: '24px',
                        fontWeight: 700,
                        color: isDarkMode ? '#e5e7eb' : '#1f2937',
                        offsetY: 5,
                        formatter: function (val) {
                            return Math.round(val) + '%';
                        }
                    },
                    total: {
                        show: true,
                        label: 'Toplam Skor',
                        fontSize: '12px',
                        fontWeight: 600,
                        color: isDarkMode ? '#9ca3af' : '#6b7280',
                        formatter: function (w) {
                            const avg = w.globals.seriesTotals.reduce((a, b) => a + b, 0) / w.globals.seriesTotals.length;
                            return Math.round(avg) + '%';
                        }
                    }
                },
                hollow: {
                    margin: 15,
                    size: '45%',
                    background: isDarkMode ? '#1f2937' : '#ffffff',
                    dropShadow: {
                        enabled: true,
                        top: 2,
                        left: 0,
                        blur: 4,
                        opacity: 0.15
                    }
                }
            }
        },
        stroke: {
            lineCap: 'round'
        },
        labels: ['İş Verimi', 'Mesai Uyumu', 'Genel Başarı'],
        legend: {
            show: true,
            position: 'bottom',
            fontSize: '13px',
            fontWeight: 500,
            labels: {
                colors: isDarkMode ? '#9ca3af' : '#6b7280'
            },
            markers: {
                width: 10,
                height: 10,
                radius: 10
            },
            itemMargin: {
                horizontal: 8,
                vertical: 5
            }
        },
        tooltip: {
            ...darkModeConfig.tooltip,
            enabled: true,
            y: {
                formatter: function (val) {
                    return Math.round(val) + '% başarı oranı';
                }
            },
            custom: function({series, seriesIndex, dataPointIndex, w}) {
                const labels = ['İş Verimi', 'Mesai Uyumu', 'Genel Başarı'];
                const colors = ['#10b981', '#3b82f6', '#8b5cf6'];
                return `
                    <div class="px-4 py-3 rounded-lg" style="background: ${isDarkMode ? '#1f2937' : '#ffffff'}; border: 2px solid ${colors[seriesIndex]};">
                        <div class="font-bold text-sm mb-1" style="color: ${isDarkMode ? '#e5e7eb' : '#1f2937'};">${labels[seriesIndex]}</div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background: ${colors[seriesIndex]};"></span>
                            <span style="color: ${isDarkMode ? '#9ca3af' : '#6b7280'};">${Math.round(series[seriesIndex])}% Başarı</span>
                        </div>
                    </div>
                `;
            }
        }
    };
    new ApexCharts(document.querySelector("#performanceRadialChart"), performanceRadialOptions).render();
</script>
@endsection
