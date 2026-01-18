@extends('layouts.master')

@section('title', 'Tagleme Başarı Oranı')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tagleme Başarı Oranı</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Süre bazlı kategorizasyon performansı</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İstatistikler</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Tagleme Oranı</span>
    </li>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 py-3">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wide">
            <i class="fas fa-filter text-primary-500"></i>
            Tarih Filtresi
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <x-datepicker 
                    label="Başlangıç" 
                    name="start_date" 
                    value="{{ $filters['start_date'] }}"
                    placeholder="Tarih seçiniz"
                />
            </div>
            <div>
                <x-datepicker 
                    label="Bitiş" 
                    name="end_date" 
                    value="{{ $filters['end_date'] }}"
                    placeholder="Tarih seçiniz"
                />
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-search mr-2"></i> Raporu Getir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="card bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium mb-1">Toplam Süre</p>
                    <h3 class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($taggingRate['total'], 2) }} <span class="text-sm font-normal">Saat</span></h3>
                </div>
                <div class="p-3 bg-blue-200 dark:bg-blue-800 rounded-full text-blue-600 dark:text-blue-200">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium mb-1">Taglenmiş</p>
                    <h3 class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($taggingRate['tagged'], 2) }} <span class="text-sm font-normal">Saat</span></h3>
                    <p class="text-xs text-green-600 mt-1 font-bold">%{{ number_format($taggingRate['tagging_rate'], 1) }} Başarı</p>
                </div>
                <div class="p-3 bg-green-200 dark:bg-green-800 rounded-full text-green-600 dark:text-green-200">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-orange-50 dark:bg-orange-900/10 border-l-4 border-orange-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-orange-600 dark:text-orange-400 font-medium mb-1">Taglenmemiş</p>
                    <h3 class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ number_format($taggingRate['untagged'], 2) }} <span class="text-sm font-normal">Saat</span></h3>
                    <p class="text-xs text-orange-600 mt-1 font-bold">%{{ number_format(100 - $taggingRate['tagging_rate'], 1) }} Eksik</p>
                </div>
                <div class="p-3 bg-orange-200 dark:bg-orange-800 rounded-full text-orange-600 dark:text-orange-200">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-purple-50 dark:bg-purple-900/10 border-l-4 border-purple-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 dark:text-purple-400 font-medium mb-1">Otomatik Tag</p>
                    <h3 class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($taggingRate['auto_tagged'], 2) }} <span class="text-sm font-normal">Saat</span></h3>
                </div>
                <div class="p-3 bg-purple-200 dark:bg-purple-800 rounded-full text-purple-600 dark:text-purple-200">
                    <i class="fas fa-magic text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Tagging Rate Chart -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white">Tagleme Başarı Oranı</h5>
        </div>
        <div class="card-body flex flex-col items-center justify-center p-6">
            <div class="w-full h-64 relative">
                <canvas id="taggingRatePieChart"></canvas>
            </div>
            <div class="text-center mt-4">
                <h2 class="text-3xl font-bold {{ $taggingRate['tagging_rate'] >= 80 ? 'text-green-500' : ($taggingRate['tagging_rate'] >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                    %{{ number_format($taggingRate['tagging_rate'], 1) }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm uppercase tracking-wide">Genel Başarı Oranı</p>
            </div>
        </div>
    </div>

    <!-- Auto vs Manual Chart -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white">Otomatik / Manuel Dağılımı</h5>
        </div>
        <div class="card-body p-6">
            <div class="w-full h-64">
                <canvas id="autoManualChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Otomatik</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($taggingRate['auto_tagged'], 2) }} Saat</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Manuel</span>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($taggingRate['manual_tagged'], 2) }} Saat</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Table -->
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-list text-primary-500"></i>
            Tagleme Durumu Özeti
        </h5>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400">Durum</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400">Süre (Saat)</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400">Yüzde Payı</th>
                        <th class="text-left px-6 py-3 text-sm font-semibold text-gray-600 dark:text-gray-400 w-1/3">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <!-- Tagged Total -->
                    <tr class="bg-green-50/50 dark:bg-green-900/10">
                        <td class="px-6 py-4 font-medium text-green-700 dark:text-green-300">Taglenmiş (Toplam)</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ number_format($taggingRate['tagged'], 2) }}</td>
                        <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400">%{{ number_format($taggingRate['tagging_rate'], 1) }}</td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $taggingRate['tagging_rate'] }}%"></div>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Sub: Auto -->
                    <tr>
                        <td class="px-6 py-4 pl-12 text-gray-600 dark:text-gray-400">
                            <i class="fas fa-level-up-alt fa-rotate-90 mr-2 text-gray-400"></i> Otomatik
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ number_format($taggingRate['auto_tagged'], 2) }}</td>
                        <td class="px-6 py-4 font-mono text-gray-500">%{{ number_format($taggingRate['auto_percentage'], 1) }}</td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $taggingRate['auto_percentage'] }}%"></div>
                            </div>
                        </td>
                    </tr>

                    <!-- Sub: Manual -->
                    <tr>
                        <td class="px-6 py-4 pl-12 text-gray-600 dark:text-gray-400">
                            <i class="fas fa-level-up-alt fa-rotate-90 mr-2 text-gray-400"></i> Manuel
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ number_format($taggingRate['manual_tagged'], 2) }}</td>
                        <td class="px-6 py-4 font-mono text-gray-500">%{{ number_format($taggingRate['manual_percentage'], 1) }}</td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $taggingRate['manual_percentage'] }}%"></div>
                            </div>
                        </td>
                    </tr>

                    <!-- Untagged -->
                    <tr class="bg-orange-50/50 dark:bg-orange-900/10">
                        <td class="px-6 py-4 font-medium text-orange-700 dark:text-orange-300">Taglenmemiş</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ number_format($taggingRate['untagged'], 2) }}</td>
                        <td class="px-6 py-4 font-mono text-gray-600 dark:text-gray-400">%{{ number_format(100 - $taggingRate['tagging_rate'], 1) }}</td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-orange-500 h-2.5 rounded-full" style="width: {{ 100 - $taggingRate['tagging_rate'] }}%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($taggingRate['untagged'] > 0)
            <div class="mt-6 p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                    <p class="text-orange-800 dark:text-orange-200">
                        Henüz <strong>{{ number_format($taggingRate['untagged'], 2) }} saatlik</strong> aktivite kategorize edilmemiş.
                    </p>
                </div>
                <a href="{{ route('activities.auto-tag') }}" class="btn btn-sm btn-primary">
                    Otomatik Tagleme Başlat
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = 'rgba(107, 114, 128, 0.1)';

    // Tagging Rate Pie Chart
    const pieCtx = document.getElementById('taggingRatePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Taglenmiş', 'Taglenmemiş'],
            datasets: [{
                data: [{{ $taggingRate['tagged'] }}, {{ $taggingRate['untagged'] }}],
                backgroundColor: ['#22c55e', '#f97316'], // green-500, orange-500
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20 } },
                tooltip: { 
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    padding: 12,
                    callbacks: {
                        label: function(context) { return context.label + ': ' + context.raw.toFixed(2) + ' Saat'; }
                    }
                }
            }
        }
    });

    // Auto vs Manual Chart
    const autoManualCtx = document.getElementById('autoManualChart').getContext('2d');
    new Chart(autoManualCtx, {
        type: 'bar',
        data: {
            labels: ['Otomatik', 'Manuel'],
            datasets: [{
                label: 'Süre (Saat)',
                data: [{{ $taggingRate['auto_tagged'] }}, {{ $taggingRate['manual_tagged'] }}],
                backgroundColor: ['#a855f7', '#3b82f6'], // purple-500, blue-500
                borderRadius: 6,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12 }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
