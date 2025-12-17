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

<!-- Ana Grafikler - 3 Sütun -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- Haftalık Ritim - 2 Sütun -->
    <div class="xl:col-span-2 card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-50 to-transparent dark:from-primary-900/20">
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
        <div class="card-body">
            <div id="weeklyRhythmChart"></div>
        </div>
    </div>

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
</div>

<!-- İş vs Diğer Karşılaştırması - YENİ -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <!-- İş/Diğer Oranı Grafik -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-50 to-transparent dark:from-green-900/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-balance-scale text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">İş vs Diğer Aktivite</h5>
                    <p class="text-xs text-gray-500">Süre bazlı karşılaştırma</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="workOtherChart"></div>
        </div>
    </div>

    <!-- Mesai İçi/Dışı Grafik - YENİ -->
    <div class="card hover:shadow-xl transition-shadow">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-transparent dark:from-blue-900/20">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h5 class="font-bold text-gray-900 dark:text-white">Mesai Saatleri Dağılımı</h5>
                    <p class="text-xs text-gray-500">İçi/dışı karşılaştırma</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="workingHoursChart"></div>
        </div>
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
    // Haftalık Ritim Grafiği - GELİŞTİRİLMİŞ
    var weeklyOptions = {
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: true },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '60%',
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "h";
            },
            offsetY: -20,
            style: {
                fontSize: '11px',
                colors: ["#304758"]
            }
        },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        series: [{
            name: 'Ortalama Çalışma (Saat)',
            data: @json(array_column($weeklyRhythm, 'avg_hours'))
        }],
        xaxis: {
            categories: @json(array_column($weeklyRhythm, 'day')),
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: { text: 'Saat' },
            labels: {
                formatter: function (val) {
                    return val.toFixed(0);
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "vertical",
                shadeIntensity: 0.25,
                inverseColors: true,
                opacityFrom: 0.85,
                opacityTo: 0.55,
                stops: [50, 0, 100]
            },
        },
        colors: ['#7366ff'],
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        }
    };
    new ApexCharts(document.querySelector("#weeklyRhythmChart"), weeklyOptions).render();

    // Kategori Dağılımı - GELİŞTİRİLMİŞ
    var categoryOptions = {
        series: @json($topCategories->pluck('percentage')),
        chart: {
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
            fontSize: '13px'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            showAlways: true,
                            show: true,
                            label: 'Toplam',
                            fontSize: '16px',
                            fontWeight: 600
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%"
            }
        }
    };
    new ApexCharts(document.querySelector("#categoryChart"), categoryOptions).render();

    // İş vs Diğer Grafik - YENİ
    var workOtherOptions = {
        series: [{
            name: 'Süre (Saat)',
            data: [
                {{ $workOtherRatio['work']['duration_hours'] }},
                {{ $workOtherRatio['other']['duration_hours'] }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 8,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + " saat";
            },
            offsetX: -6,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            }
        },
        xaxis: {
            categories: ['İş Aktiviteleri', 'Diğer Aktiviteler']
        },
        colors: ['#51bb25', '#f73164'],
        grid: {
            borderColor: '#e7e7e7'
        }
    };
    new ApexCharts(document.querySelector("#workOtherChart"), workOtherOptions).render();

    // Mesai Saatleri Grafik - YENİ
    var workingHoursOptions = {
        series: [{
            name: 'İş',
            data: [
                {{ $workingHourStats['working_hours']['work'] ?? 0 }},
                {{ $workingHourStats['outside_hours']['work'] ?? 0 }}
            ]
        }, {
            name: 'Diğer',
            data: [
                {{ $workingHourStats['working_hours']['other'] ?? 0 }},
                {{ $workingHourStats['outside_hours']['other'] ?? 0 }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 8,
                columnWidth: '55%'
            }
        },
        xaxis: {
            categories: ['Mesai İçi', 'Mesai Dışı']
        },
        colors: ['#51bb25', '#f8d62b'],
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        fill: {
            opacity: 1
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(0) + "h";
            }
        }
    };
    new ApexCharts(document.querySelector("#workingHoursChart"), workingHoursOptions).render();

    // Top Keywords - Bar Chart
    var topKeywordsOptions = {
        series: [{
            name: 'Kullanım Sayısı',
            data: @json($topKeywords->pluck('count')->take(10)->toArray())
        }],
        chart: {
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
            }
        },
        xaxis: {
            categories: @json($topKeywords->pluck('keyword')->take(10)->toArray())
        },
        colors: ['#7366ff', '#51bb25', '#f73164', '#f8d62b', '#a927f9', '#00d0ff', '#ff6384', '#36a2eb', '#cc65fe', '#ffce56'],
        legend: {
            show: false
        }
    };
    new ApexCharts(document.querySelector("#topKeywordsChart"), topKeywordsOptions).render();

    // Top Processes - Bar Chart
    var topProcessesOptions = {
        series: [{
            name: 'Süre (Saat)',
            data: @json($topProcesses->pluck('duration_hours')->take(10)->map(function($item) { return (float)$item; })->toArray())
        }],
        chart: {
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
            }
        },
        xaxis: {
            categories: @json($topProcesses->pluck('process_name')->take(10)->map(function($item) { return strlen($item) > 20 ? substr($item, 0, 20) . '...' : $item; })->toArray())
        },
        colors: ['#f73164', '#51bb25', '#7366ff', '#f8d62b', '#a927f9', '#00d0ff', '#ff6384', '#36a2eb', '#cc65fe', '#ffce56'],
        legend: {
            show: false
        }
    };
    new ApexCharts(document.querySelector("#topProcessesChart"), topProcessesOptions).render();
</script>
@endsection
