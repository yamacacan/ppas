@extends('layouts.master')

@section('title', 'Kullanıcı İstatistikleri')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name ?? $user->username }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kullanıcı detayları ve aktivite analizi</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('computer-users.index') }}" class="text-primary-600 hover:text-primary-700">Kullanıcılar</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İstatistikler</span>
    </li>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-6">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 py-3 flex justify-between items-center">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wide">
            <i class="fas fa-filter text-primary-500"></i>
            Tarih Filtresi
        </h5>
        @if($user->motherboard_uuid)
            <a href="{{ route('computers.show', $user->motherboard_uuid) }}" 
               class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-desktop mr-2"></i>
                Bilgisayar Detayları
            </a>
        @endif
    </div>
    <div class="card-body">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Başlangıç</label>
                <x-datepicker 
                       name="start_date" 
                       value="{{ $filters['start_date'] ?? '' }}"
                       placeholder="Başlangıç Tarihi"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bitiş</label>
                <x-datepicker 
                       name="end_date" 
                       value="{{ $filters['end_date'] ?? '' }}"
                       placeholder="Bitiş Tarihi"
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

<!-- Performance Content -->
<div>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500 text-center">
            <div class="card-body py-6">
                <h2 class="text-4xl font-bold text-blue-700 dark:text-blue-300 mb-1">
                    {{ number_format($workOtherRatio['work']['duration_hours'], 1) }}
                </h2>
                <p class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wider">İş Saatleri</p>
            </div>
        </div>

        <div class="card bg-gray-50 dark:bg-gray-800 border-l-4 border-gray-500 text-center">
            <div class="card-body py-6">
                <h2 class="text-4xl font-bold text-gray-700 dark:text-gray-300 mb-1">
                    {{ number_format($workOtherRatio['other']['duration_hours'], 1) }}
                </h2>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Diğer Saatler</p>
            </div>
        </div>

        <div class="card bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500 text-center">
            <div class="card-body py-6">
                <h2 class="text-4xl font-bold text-green-700 dark:text-green-300 mb-1">
                    {{ number_format($workOtherRatio['total']['duration_hours'], 1) }}
                </h2>
                <p class="text-sm font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Toplam Saat</p>
            </div>
        </div>
    </div>

    <!-- Working Hours & Weekly Rhythm -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Working Hours Efficiency -->
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700 relative">
                <h5 class="font-bold text-gray-900 dark:text-white">
                    Mesai Saatleri Verimliliği 
                    ({{ \App\Models\FirmSettings::instance()->work_start_time }} - {{ \App\Models\FirmSettings::instance()->work_end_time }})
                </h5>
            </div>
            <div class="card-body p-6">
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/10 rounded-lg p-4 text-center">
                        <p class="text-xs text-blue-600 dark:text-blue-400 uppercase">Mesai İçinde</p>
                        <h3 class="text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1">
                            {{ number_format($workingHourStats['working_hours']['work'], 2) }} Saat
                        </h3>
                        <p class="text-xs text-blue-500 mt-1">İş Aktivitesi</p>
                    </div>
                    <div class="bg-orange-50 dark:bg-orange-900/10 rounded-lg p-4 text-center">
                        <p class="text-xs text-orange-600 dark:text-orange-400 uppercase">Mesai Dışında</p>
                        <h3 class="text-2xl font-bold text-orange-900 dark:text-orange-100 mt-1">
                            {{ number_format($workingHourStats['outside_hours']['work'], 2) }} Saat
                        </h3>
                        <p class="text-xs text-orange-500 mt-1">İş Aktivitesi</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Toplam Mesai İçi Aktivite</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ number_format($workingHourStats['working_hours']['total'], 2) }} Saat</span>
                        </div>
                        @php
                            $whTotal = $workingHourStats['working_hours']['total'] > 0 ? $workingHourStats['working_hours']['total'] : 1;
                            $whWorkPct = ($workingHourStats['working_hours']['work'] / $whTotal) * 100;
                        @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-blue-600 h-2.5 rounded-full relative" style="width: {{ $whWorkPct }}%">
                                <span class="sr-only">Work</span>
                            </div>
                        </div>
                        <p class="text-xs text-right mt-1 text-blue-600 dark:text-blue-400 font-medium">%{{ number_format($whWorkPct, 1) }} Verimlilik (İş/Toplam)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Rhythm -->
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white">Haftalık Ritim (Ortalama İş Saati)</h5>
            </div>
            <div class="card-body p-6">
                <div class="w-full h-64">
                    <canvas id="weeklyRhythmChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Pie Chart -->
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white">Genel İş / Diğer Dağılımı</h5>
            </div>
            <div class="card-body flex items-center justify-center p-6">
                <div class="w-full h-64">
                    <canvas id="workOtherPieChart"></canvas>
                </div>
                <div class="ml-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">İş (%{{ number_format($workOtherRatio['work']['percentage'], 1) }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-gray-400"></span>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Diğer (%{{ number_format($workOtherRatio['other']['percentage'], 1) }})</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Categories -->
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white">En Çok Kullanılan Kategoriler</h5>
            </div>
            <div class="card-body p-6">
                <div class="w-full h-64">
                    <canvas id="topCategoriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Keywords and Apps -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Keywords -->
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-key text-primary-500"></i>
                    En Çok Eşleşen Keywordler
                </h5>
            </div>
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left">Keyword</th>
                                <th class="px-4 py-2 text-right">Sayaç</th>
                                <th class="px-4 py-2 text-right">Toplam Süre (Saat)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($topKeywords as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2 font-mono text-primary-600 dark:text-primary-400">{{ $item['keyword'] }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($item['count']) }}</td>
                                <td class="px-4 py-2 text-right font-bold">{{ number_format($item['duration_hours'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">Veri yok</td> 
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Apps -->
        <div class="card h-full">
            <div class="card-header border-b border-gray-200 dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-microchip text-purple-500"></i>
                    En Çok Kullanılan Uygulamalar
                </h5>
            </div>
            <div class="card-body">
                    <div class="overflow-x-auto">
                    <table class="table w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-2 text-left">Uygulama</th>
                                <th class="px-4 py-2 text-right">Toplam Süre (Saat)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($topProcesses as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-2">
                                    <code class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-xs">{{ $item['process_name'] }}</code>
                                </td>
                                <td class="px-4 py-2 text-right font-bold">{{ number_format($item['duration_hours'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-center text-gray-500">Veri yok</td> 
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-history text-primary-500"></i>
                Son Aktiviteler
            </h5>
        </div>
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Process</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Başlık</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Kategoriler</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Tarih</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500">Süre</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentActivities as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-pink-600 dark:text-pink-400 font-mono">
                                    {{ Str::limit($activity->process_name, 20) }}
                                </code>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ Str::limit($activity->title, 40) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($activity->categories as $category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $activity->start_time_utc->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                                {{ $activity->duration_formatted }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3 opacity-30"></i>
                                <p>Bu tarih aralığında aktivite bulunamadı</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                backgroundColor: ['#3b82f6', '#9ca3af'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { 
                    backgroundColor: 'rgba(17, 24, 39, 0.9)', 
                    padding: 12,
                    callbacks: { label: function(context) { return context.label + ': ' + context.raw.toFixed(1) + ' Saat'; } }
                }
            }
        }
    });

    // Top Categories Chart
    const topCatCtx = document.getElementById('topCategoriesChart').getContext('2d');
    new Chart(topCatCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topCategories->pluck('name')->toArray()) !!},
            datasets: [{
                label: 'Süre (Saat)',
                data: {!! json_encode($topCategories->pluck('total_duration_hours')->toArray()) !!},
                backgroundColor: '#3b82f6',
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

    // Weekly Rhythm Chart
    const rhythmData = {!! json_encode($weeklyRhythm) !!};
    const rhythmCtx = document.getElementById('weeklyRhythmChart').getContext('2d');
    new Chart(rhythmCtx, {
        type: 'bar',
        data: {
            labels: rhythmData.map(d => d.day),
            datasets: [{
                label: 'Ortalama İş Süresi (Saat)',
                data: rhythmData.map(d => d.avg_hours),
                backgroundColor: '#8b5cf6', // purple-500
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
</script>
@endsection
