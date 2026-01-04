@extends('layouts.master')

@section('title', 'Kategori İstatistikleri')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori İstatistikleri</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detaylı kategori analizleri ve grafikler</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İstatistikler</span>
    </li>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-filter text-primary-500"></i>
            Tarih Filtresi
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <x-datepicker 
                    label="Başlangıç Tarihi" 
                    name="start_date" 
                    value="{{ $filters['start_date'] }}"
                    placeholder="Tarih seçiniz"
                />
            </div>
            <div>
                <x-datepicker 
                    label="Bitiş Tarihi" 
                    name="end_date" 
                    value="{{ $filters['end_date'] }}"
                    placeholder="Tarih seçiniz"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hızlı Seçim</label>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-outline-primary flex-1 text-xs" onclick="setDateRange(7)">
                        Son 7 Gün
                    </button>
                    <button type="button" class="btn btn-outline-primary flex-1 text-xs" onclick="setDateRange(30)">
                        Son 30 Gün
                    </button>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-search mr-2"></i> İstatistikleri Getir
                </button>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- Main Chart -->
    <div class="xl:col-span-2">
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white">Kategori Bazlı Aktivite Dağılımı</h5>
            </div>
            <div class="card-body">
                <div class="relative h-80 w-full">
                    <canvas id="categoryDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart -->
    <div class="xl:col-span-1">
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white">En Çok Kullanılan 5 Kategori</h5>
            </div>
            <div class="card-body">
                <div class="relative h-64 w-full">
                    <canvas id="topCategoriesChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($statistics['categories']->take(5) as $cat)
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'][$loop->index] }}"></span>
                            <span class="text-gray-600 dark:text-gray-400">{{ Str::limit($cat['name'], 20) }}</span>
                        </span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($cat['total_duration_hours'], 1) }} Saat</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Table -->
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-table text-primary-500"></i>
                Kategori Detayları
            </h5>
            <button class="btn btn-secondary text-sm">
                <i class="fas fa-download mr-1"></i> Excel İndir
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left">Kategori</th>
                        <th class="px-6 py-3 text-left">Tip</th>
                        <th class="px-6 py-3 text-left">Aktivite Sayısı</th>
                        <th class="px-6 py-3 text-left">Toplam Süre</th>
                        <th class="px-6 py-3 text-left">Ortalama Süre</th>
                        <th class="px-6 py-3 text-left w-1/4">Yüzde Payı</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($statistics['categories'] as $cat)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-500">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $cat['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($cat['type'] == 'work')
                                <span class="badge badge-primary">
                                    <i class="fas fa-briefcase mr-1"></i> İş
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-coffee mr-1"></i> Diğer
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ number_format($cat['activity_count']) }}
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-900 dark:text-white">
                            {{ gmdate('H:i:s', $cat['total_duration_seconds']) }}
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-500">
                            {{ gmdate('H:i:s', $cat['avg_duration_seconds']) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-1">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $cat['percentage'] }}%"></div>
                            </div>
                            <div class="text-xs text-right text-gray-500 dark:text-gray-400">
                                %{{ number_format($cat['percentage'], 1) }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-chart-pie text-4xl mb-3 opacity-30"></i>
                            <p>Gösterilecek veri bulunamadı</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setDateRange(days) {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - days);
        
        document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
        document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
    }

    // Common Chart Options
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = 'rgba(107, 114, 128, 0.1)';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Kategori Dağılım Chart
    const distributionCtx = document.getElementById('categoryDistributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($statistics['categories']->pluck('name')) !!},
            datasets: [{
                label: 'Toplam Süre (Saat)',
                data: {!! json_encode($statistics['categories']->pluck('total_duration_hours')) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4,
                hoverBackgroundColor: '#2563eb'
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
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.raw.toFixed(2) + ' Saat';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 2] }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Top Kategoriler Pie Chart
    const topCatData = {!! json_encode($statistics['categories']->take(5)) !!};
    const topCatCtx = document.getElementById('topCategoriesChart').getContext('2d');
    new Chart(topCatCtx, {
        type: 'doughnut',
        data: {
            labels: topCatData.map(c => c.name),
            datasets: [{
                data: topCatData.map(c => c.total_duration_hours),
                backgroundColor: [
                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            label += context.raw.toFixed(2) + ' Saat';
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
