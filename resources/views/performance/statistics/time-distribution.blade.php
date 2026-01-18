@extends('layouts.master')

@section('title', 'Zaman Dağılımı')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Zaman Bazlı Dağılım</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sürecin zaman içindeki değişimi ve trendler</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İstatistikler</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Zaman Dağılımı</span>
    </li>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 py-3">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wide">
            <i class="fas fa-filter text-primary-500"></i>
            Analiz Filtreleri
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-datepicker 
                        label="Başlangıç" 
                        name="start_date" 
                        value="{{ $startDate }}"
                        placeholder="Tarih seçiniz"
                    />
                </div>
                <div>
                    <x-datepicker 
                        label="Bitiş" 
                        name="end_date" 
                        value="{{ $endDate }}"
                        placeholder="Tarih seçiniz"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gruplama</label>
                    <select name="group_by" class="form-select w-full">
                        <option value="hour" {{ $groupBy == 'hour' ? 'selected' : '' }}>Saatlik</option>
                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Günlük</option>
                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Haftalık</option>
                        <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Aylık</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-sync-alt mr-2"></i> Güncelle
                    </button>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setQuickFilter('today')">Bugün</button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setQuickFilter('week')">Bu Hafta</button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setQuickFilter('month')">Bu Ay</button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setQuickFilter('last7')">Son 7 Gün</button>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setQuickFilter('last30')">Son 30 Gün</button>
            </div>
        </form>
    </div>
</div>

<!-- Main Trend Chart -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white">Genel Trend ({{ ucfirst($groupBy) }} Bazlı)</h5>
    </div>
    <div class="card-body">
        <div class="w-full h-80">
            <canvas id="timeDistributionChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Work vs Other Trend -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white">İş vs Diğer (Zamanla Değişim)</h5>
        </div>
        <div class="card-body p-6">
            <div class="w-full h-64">
                <canvas id="countChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Duration Distribution -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white">Toplam Süre Dağılımı</h5>
        </div>
        <div class="card-body p-6">
            <div class="w-full h-64">
                <canvas id="durationChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Details Table -->
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-table text-primary-500"></i>
            Detaylı Zaman tablosu
        </h5>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Periyot</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Aktivite</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Toplam Süre</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Ortalama</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">İş (Saat)</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Diğer (Saat)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($distribution as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            {{ $item['period'] }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ number_format($item['activity_count']) }}
                        </td>
                        <td class="px-6 py-4 font-mono text-gray-800 dark:text-gray-200">
                            @php
                                $totalSeconds = $item['total_duration_seconds'];
                                $hours = floor($totalSeconds / 3600);
                                $minutes = floor(($totalSeconds % 3600) / 60);
                                $seconds = $totalSeconds % 60;
                            @endphp
                            {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-sm">
                            @php
                                $avgSeconds = $item['avg_duration_seconds'];
                                $avgHours = floor($avgSeconds / 3600);
                                $avgMinutes = floor(($avgSeconds % 3600) / 60);
                                $avgSecs = $avgSeconds % 60;
                            @endphp
                            {{ sprintf('%02d:%02d:%02d', $avgHours, $avgMinutes, $avgSecs) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                {{ number_format($item['work_count'], 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ number_format($item['other_count'], 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            Veri bulunamadı
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-800/50 font-bold text-gray-900 dark:text-white">
                    <tr>
                        <td class="px-6 py-4">TOPLAM</td>
                        <td class="px-6 py-4">{{ number_format(array_sum(array_column($distribution, 'activity_count'))) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $totalAllSeconds = array_sum(array_column($distribution, 'total_duration_seconds'));
                                $totalHours = floor($totalAllSeconds / 3600);
                                $totalMinutes = floor(($totalAllSeconds % 3600) / 60);
                                $totalSecs = $totalAllSeconds % 60;
                            @endphp
                            {{ sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSecs) }}
                        </td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4 text-blue-600 dark:text-blue-400">{{ number_format(array_sum(array_column($distribution, 'work_count')), 2) }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ number_format(array_sum(array_column($distribution, 'other_count')), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = 'rgba(107, 114, 128, 0.1)';

    function setQuickFilter(type) {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        const groupBySelect = document.querySelector('select[name="group_by"]');
        const now = new Date();
        
        // Helper to format date as YYYY-MM-DD
        const fmt = d => d.toISOString().split('T')[0];

        switch(type) {
            case 'today':
                startDateInput.value = fmt(now);
                endDateInput.value = fmt(now);
                groupBySelect.value = 'hour';
                break;
            case 'week':
                const weekStart = new Date();
                weekStart.setDate(now.getDate() - now.getDay() + (now.getDay() == 0 ? -6:1)); // Start of week (Monday) ? Logic varies, simple approach:
                // Actually let's just use "last 7 days" styled logic but aligned to week if possible. 
                // Or simply:
                const d = new Date();
                d.setDate(d.getDate() - d.getDay() + 1);
                startDateInput.value = fmt(d);
                endDateInput.value = fmt(new Date());
                groupBySelect.value = 'day';
                break;
            case 'month':
                const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);
                startDateInput.value = fmt(monthStart);
                endDateInput.value = fmt(new Date());
                groupBySelect.value = 'day';
                break;
            case 'last7':
                const last7 = new Date();
                last7.setDate(last7.getDate() - 7);
                startDateInput.value = fmt(last7);
                endDateInput.value = fmt(new Date());
                groupBySelect.value = 'day';
                break;
            case 'last30':
                const last30 = new Date();
                last30.setDate(last30.getDate() - 30);
                startDateInput.value = fmt(last30);
                endDateInput.value = fmt(new Date());
                groupBySelect.value = 'day';
                break;
        }
    }

    const distributionData = {!! json_encode($distribution) !!};

    // Main Trend Chart
    const lineCtx = document.getElementById('timeDistributionChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: distributionData.map(d => d.period),
            datasets: [{
                label: 'Toplam Süre (Saat)',
                data: distributionData.map(d => d.total_duration_hours),
                borderColor: '#3b82f6', // blue-500
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    padding: 12,
                    callbacks: { label: c => c.dataset.label + ': ' + c.raw + ' Saat' }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 2] } },
                x: { grid: { display: false } }
            }
        }
    });

    // Duration Bar Chart
    const durationCtx = document.getElementById('durationChart').getContext('2d');
    new Chart(durationCtx, {
        type: 'bar',
        data: {
            labels: distributionData.map(d => d.period),
            datasets: [{
                label: 'Toplam Süre (saat)',
                data: distributionData.map(d => d.total_duration_hours),
                backgroundColor: '#10b981', // green-500
                borderRadius: 4
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

    // Work vs Other Chart
    const countCtx = document.getElementById('countChart').getContext('2d');
    new Chart(countCtx, {
        type: 'bar',
        data: {
            labels: distributionData.map(d => d.period),
            datasets: [
                {
                    label: 'İş (Saat)',
                    data: distributionData.map(d => d.work_count),
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                },
                {
                    label: 'Diğer (Saat)',
                    data: distributionData.map(d => d.other_count),
                    backgroundColor: '#9ca3af',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12 }
            },
            scales: {
                y: { beginAtZero: true, stacked: true, grid: { borderDash: [2, 2] } },
                x: { stacked: true, grid: { display: false } }
            }
        }
    });
</script>
@endsection
