@extends('layouts.master')

@section('title', 'İş/Diğer Oranı')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">İş / Diğer Analizi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Verimlilik odaklı aktivite karşılaştırması</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İstatistikler</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İş/Diğer</span>
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
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hızlı Seçim</label>
                <div class="flex rounded-md shadow-sm" role="group">
                    <button type="button" onclick="setDateRange(7)" class="flex-1 px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600">
                        7 Gün
                    </button>
                    <button type="button" onclick="setDateRange(30)" class="flex-1 px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600">
                        30 Gün
                    </button>
                    <button type="button" onclick="setDateRange(90)" class="flex-1 px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600">
                        90 Gün
                    </button>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-search mr-2"></i> Filtrele
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-bold text-blue-900 dark:text-blue-100 mb-1">
                        {{ number_format($workOtherRatio['work']['duration_hours'], 1) }}
                    </h3>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">İş Saatleri</p>
                    <p class="text-xs text-blue-500 mt-2">
                        <i class="fas fa-check-double mr-1"></i>
                        {{ number_format($workOtherRatio['work']['activity_count']) }} Aktivite
                    </p>
                </div>
                <div class="p-3 bg-blue-200 dark:bg-blue-800 rounded-full text-blue-600 dark:text-blue-200">
                    <i class="fas fa-briefcase text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-gray-50 dark:bg-gray-800 border-l-4 border-gray-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ number_format($workOtherRatio['other']['duration_hours'], 1) }}
                    </h3>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wide">Diğer Saatler</p>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-coffee mr-1"></i>
                        {{ number_format($workOtherRatio['other']['activity_count']) }} Aktivite
                    </p>
                </div>
                <div class="p-3 bg-gray-200 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-200">
                    <i class="fas fa-gamepad text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500">
        <div class="card-body p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-3xl font-bold text-green-900 dark:text-green-100 mb-1">
                        {{ number_format($workOtherRatio['total']['duration_hours'], 1) }}
                    </h3>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Toplam Saat</p>
                    <p class="text-xs text-green-500 mt-2">
                        <i class="fas fa-clock mr-1"></i>
                        {{ number_format($workOtherRatio['total']['activity_count']) }} Aktivite
                    </p>
                </div>
                <div class="p-3 bg-green-200 dark:bg-green-800 rounded-full text-green-600 dark:text-green-200">
                    <i class="fas fa-history text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Pie Chart -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white">Oransal Dağılım</h5>
        </div>
        <div class="card-body p-6">
            <div class="w-full h-64">
                <canvas id="workOtherPieChart"></canvas>
            </div>
            <div class="mt-6 flex justify-center gap-8">
                <div class="text-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">İş</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">%{{ number_format($workOtherRatio['work']['percentage'], 1) }}</span>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                        <span class="text-gray-600 dark:text-gray-400 text-sm">Diğer</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">%{{ number_format($workOtherRatio['other']['percentage'], 1) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparative Chart -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white">Saat Karşılaştırması</h5>
        </div>
        <div class="card-body p-6 flex flex-col justify-center">
            <div class="w-full h-64">
                <canvas id="activityCountChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Stats -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Work Details -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-blue-50/50 dark:bg-blue-900/10">
            <h5 class="font-bold text-blue-800 dark:text-blue-300 flex items-center gap-2">
                <i class="fas fa-briefcase"></i>
                İş Aktiviteleri Detayı
            </h5>
        </div>
        <div class="card-body">
            <dl class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Aktivite Sayısı</dt>
                    <dd class="font-bold text-gray-900 dark:text-white">{{ number_format($workOtherRatio['work']['activity_count']) }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Toplam Süre</dt>
                    <dd class="font-bold text-gray-900 dark:text-white">{{ number_format($workOtherRatio['work']['duration_hours'], 2) }} Saat</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Ortalama Süre</dt>
                    <dd class="font-mono text-gray-600 dark:text-gray-400">{{ number_format($workOtherRatio['work']['avg_duration_minutes'], 1) }} dk</dd>
                </div>
                <div class="pt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Verimlilik Payı</span>
                        <span class="font-bold">%{{ number_format($workOtherRatio['work']['percentage'], 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $workOtherRatio['work']['percentage'] }}%"></div>
                    </div>
                </div>
            </dl>
        </div>
    </div>

    <!-- Other Details -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            <h5 class="font-bold text-gray-800 dark:text-gray-300 flex items-center gap-2">
                <i class="fas fa-coffee"></i>
                Diğer Aktiviteler Detayı
            </h5>
        </div>
        <div class="card-body">
            <dl class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Aktivite Sayısı</dt>
                    <dd class="font-bold text-gray-900 dark:text-white">{{ number_format($workOtherRatio['other']['activity_count']) }}</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Toplam Süre</dt>
                    <dd class="font-bold text-gray-900 dark:text-white">{{ number_format($workOtherRatio['other']['duration_hours'], 2) }} Saat</dd>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                    <dt class="text-gray-600 dark:text-gray-400">Ortalama Süre</dt>
                    <dd class="font-mono text-gray-600 dark:text-gray-400">{{ number_format($workOtherRatio['other']['avg_duration_minutes'], 1) }} dk</dd>
                </div>
                <div class="pt-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Verimsizlik Payı</span>
                        <span class="font-bold">%{{ number_format($workOtherRatio['other']['percentage'], 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-gray-500 h-2 rounded-full" style="width: {{ $workOtherRatio['other']['percentage'] }}%"></div>
                    </div>
                </div>
            </dl>
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

    function setDateRange(days) {
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - days);
        const fmt = d => d.toISOString().split('T')[0];
        
        document.querySelector('input[name="start_date"]').value = fmt(startDate);
        document.querySelector('input[name="end_date"]').value = fmt(endDate);
    }

    // Pie Chart
    const pieCtx = document.getElementById('workOtherPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['İş', 'Diğer'],
            datasets: [{
                data: [
                    {{ $workOtherRatio['work']['duration_hours'] }},
                    {{ $workOtherRatio['other']['duration_hours'] }}
                ],
                backgroundColor: ['#2563eb', '#9ca3af'], // blue-600, gray-400
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { display: false },
                tooltip: { 
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    padding: 12,
                    callbacks: { label: c => c.label + ': ' + c.raw.toFixed(1) + ' Saat' }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('activityCountChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['İş', 'Diğer'],
            datasets: [{
                label: 'Süre (Saat)',
                data: [
                    {{ $workOtherRatio['work']['duration_hours'] }},
                    {{ $workOtherRatio['other']['duration_hours'] }}
                ],
                backgroundColor: ['#2563eb', '#9ca3af'],
                borderRadius: 8,
                barPercentage: 0.5
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
