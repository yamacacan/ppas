@extends('layouts.master')

@section('title', $unit->name . ' - Birim İstatistikleri')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $unit->name }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Birim performans analizi ve detayları</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('unit-statistics.index') }}" class="text-primary-600 hover:text-primary-700">Birimler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">İstatistikler</span>
    </li>
@endsection

@section('content')
<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">

<!-- Filter Card -->
<div class="card mb-6" 
     x-show="loaded" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 py-3 flex justify-between items-center">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm uppercase tracking-wide">
            <i class="fas fa-filter text-primary-500"></i>
            Tarih Filtresi
        </h5>
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                <i class="fas fa-users mr-1"></i> {{ $unit->computer_users_count ?? 0 }} Personel
            </span>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Başlangıç</label>
                <div class="relative">
                    <input type="date" name="start_date" class="datepicker form-input w-full pl-10" 
                           value="{{ $filters['start_date'] ?? '' }}" placeholder="Tarih seçin...">
                    <i class="fas fa-calendar absolute left-3 top-3 text-gray-400 pointer-events-none"></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bitiş</label>
                <div class="relative">
                    <input type="date" name="end_date" class="datepicker form-input w-full pl-10" 
                           value="{{ $filters['end_date'] ?? '' }}" placeholder="Tarih seçin...">
                    <i class="fas fa-calendar absolute left-3 top-3 text-gray-400 pointer-events-none"></i>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-search mr-2"></i> Raporu Getir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Premium Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8"
     x-show="loaded" 
     x-transition:enter="transition ease-out duration-500 delay-100"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">
    <!-- Toplam Çalışma (Purple) -->
    <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-6 relative overflow-hidden group hover:shadow-lg transition-all border border-gray-800">
        <div class="relative z-10">
            <p class="text-gray-400 text-sm font-medium mb-1">Toplam Çalışma</p>
            <h3 class="text-3xl font-bold text-white mb-2">
                <span id="counter-total-work">{{ number_format($workOtherRatio['total']['duration_hours'], 1) }}</span>
            </h3>
            <p class="text-xs text-gray-500">saat aktivite</p>
        </div>
        <div class="absolute right-5 top-6 w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
            <i class="fas fa-clock text-white text-xl"></i>
        </div>
        <div class="absolute bottom-4 right-5 text-purple-400 text-xs flex items-center gap-1">
            <span>{{ $unit->computer_users_count }} personel</span>
            <i class="fas fa-chart-line"></i>
        </div>
    </div>

    <!-- Verim Oranı (Green) -->
    <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-6 relative overflow-hidden group hover:shadow-lg transition-all border border-gray-800">
        <div class="relative z-10">
            <p class="text-gray-400 text-sm font-medium mb-1">Verim Oranı</p>
            <h3 class="text-3xl font-bold text-green-500 mb-2">
                %<span id="counter-efficiency">{{ number_format($workOtherRatio['work']['percentage'], 2) }}</span>
            </h3>
            <p class="text-xs text-gray-500">{{ number_format($workOtherRatio['work']['duration_hours'], 1) }} saat iş</p>
            
            <!-- Tailwind Progress Bar -->
            <div class="w-full bg-gray-700 rounded-full h-1.5 mt-4 overflow-hidden">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $workOtherRatio['work']['percentage'] }}%"></div>
            </div>
        </div>
        <div class="absolute right-5 top-6 w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
            <i class="fas fa-chart-bar text-white text-xl"></i>
        </div>
    </div>

    <!-- Personel (Blue) -->
    <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-6 relative overflow-hidden group hover:shadow-lg transition-all border border-gray-800">
        <div class="relative z-10">
            <p class="text-gray-400 text-sm font-medium mb-1">Personel</p>
            <h3 class="text-3xl font-bold text-blue-500 mb-2">
                <span id="counter-personnel">{{ $unit->computer_users_count }}</span>
            </h3>
            <p class="text-xs text-gray-500">toplam kullanıcı</p>
            <p class="text-xs text-blue-400 mt-4 flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Aktif çalışan
            </p>
        </div>
        <div class="absolute right-5 top-6 w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
            <i class="fas fa-users text-white text-xl"></i>
        </div>
    </div>

    <!-- Mesai Dışı (Orange) -->
    <div class="bg-gray-900 dark:bg-gray-800 rounded-2xl p-6 relative overflow-hidden group hover:shadow-lg transition-all border border-gray-800">
        <div class="relative z-10">
            <p class="text-gray-400 text-sm font-medium mb-1">Mesai Dışı</p>
            <h3 class="text-3xl font-bold text-orange-500 mb-2">
                <span id="counter-overtime">{{ number_format($workingHourStats['outside_hours']['work'], 2) }}</span>
            </h3>
            <p class="text-xs text-gray-500">saat iş aktivitesi</p>
            <p class="text-xs text-orange-400 mt-4 flex items-center gap-1">
                <i class="fas fa-exclamation-triangle"></i> Fazla mesai
            </p>
        </div>
        <div class="absolute right-5 top-6 w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform">
            <i class="fas fa-moon text-white text-xl"></i>
        </div>
    </div>
</div>

<!-- Working Hours Efficiency (Tailwind Styled Chart) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6"
     x-show="loaded" 
     x-transition:enter="transition ease-out duration-500 delay-200"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">
    <!-- Custom Tailwind Chart Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 h-full flex flex-col">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="w-2 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></span>
                Mesai Verimlilik Analizi
            </h5>
            <p class="text-sm text-gray-500 mt-1">Mesai içi vs dışı çalışma dağılımı</p>
        </div>
        <div class="p-8 flex-1 flex flex-col justify-center">
            
            <!-- Visualization Container -->
            <div class="space-y-8">
                
                <!-- Mesai İçi Row -->
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mesai İçi</span>
                            <span class="text-xs text-gray-500 block">09:00 - 18:00</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($workingHourStats['working_hours']['work'], 1) }}h</span>
                        </div>
                    </div>
                    <!-- Custom Stacked Bar -->
                    <div class="h-4 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex">
                        @php
                            $totalWorking = $workingHourStats['working_hours']['total'] > 0 ? $workingHourStats['working_hours']['total'] : 1;
                            $workPct = ($workingHourStats['working_hours']['work'] / $totalWorking) * 100;
                            $otherPct = 100 - $workPct;
                        @endphp
                        <div class="h-full bg-blue-500 hover:bg-blue-400 transition-all cursor-help relative group" style="width: {{ $workPct }}%">
                            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                İş: %{{ number_format($workPct, 1) }}
                            </div>
                        </div>
                        <div class="h-full bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 transition-all cursor-help relative group" style="width: {{ $otherPct }}%">
                            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                Diğer: %{{ number_format($otherPct, 1) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mesai Dışı Row -->
                <div>
                    <div class="flex justify-between items-end mb-2">
                         <div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Mesai Dışı</span>
                            <span class="text-xs text-gray-500 block">18:00 - 09:00</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-orange-500">{{ number_format($workingHourStats['outside_hours']['work'], 1) }}h</span>
                        </div>
                    </div>
                    <!-- Custom Stacked Bar -->
                    <div class="h-4 w-full bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden flex">
                        @php
                            $totalOutside = $workingHourStats['outside_hours']['total'] > 0 ? $workingHourStats['outside_hours']['total'] : 1;
                            $outWorkPct = ($workingHourStats['outside_hours']['work'] / $totalOutside) * 100;
                            $outOtherPct = 100 - $outWorkPct;
                        @endphp
                        <div class="h-full bg-orange-500 hover:bg-orange-400 transition-all cursor-help relative group" style="width: {{ $outWorkPct }}%">
                             <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                İş: %{{ number_format($outWorkPct, 1) }}
                            </div>
                        </div>
                        <div class="h-full bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 transition-all cursor-help relative group" style="width: {{ $outOtherPct }}%">
                             <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                Diğer: %{{ number_format($outOtherPct, 1) }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Legend -->
            <div class="flex items-center justify-center gap-6 mt-8">
                 <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Verimli Zaman</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Ekstra Efor</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Diğer</span>
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

<!-- Pie Chart & Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6"
     x-show="loaded" 
     x-transition:enter="transition ease-out duration-500 delay-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">
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

<!-- Keywords & Apps -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6"
     x-show="loaded" 
     x-transition:enter="transition ease-out duration-500 delay-400"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">
    <!-- Keywords -->
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
                            <td class="px-4 py-2 font-mono text-primary-600 dark:text-primary-400">{{ $item->keyword }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($item->count) }}</td>
                            <td class="px-4 py-2 text-right font-bold">{{ number_format($item->duration_hours, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Veri yok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Apps -->
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
                                <code class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-xs">{{ $item->process_name }}</code>
                            </td>
                            <td class="px-4 py-2 text-right font-bold">{{ number_format($item->duration_hours, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="px-4 py-4 text-center text-gray-500">Veri yok</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card"
     x-show="loaded" 
     x-transition:enter="transition ease-out duration-500 delay-500"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-users text-primary-500"></i>
            Birim Personelleri
        </h5>
    </div>
    <div class="card-body">
        <div class="overflow-x-auto">
             <table class="table w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Personel</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Bilgisayar</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-500">Toplam Süre</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-500">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($computerUsers as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                    {{ substr($user->name ?? $user->username, 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $user->name ?? $user->username }}</span>
                            </div>
                        </td>
                         <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->computer_name }}
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700 dark:text-gray-300">
                            {{ number_format($user->total_duration_hours, 1) }}h
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('computer-users.show', $user->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 transition-all shadow-sm">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">Personel bulunamadı</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
@endsection

@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
<style>
    .flatpickr-calendar { background: #1f2937 !important; border: 1px solid #374151 !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important; }
    .flatpickr-day.selected { background: #2563eb !important; border-color: #2563eb !important; }
    .flatpickr-day:hover { background: #374151 !important; }
    .flatpickr-months .flatpickr-month { background: #1f2937 !important; color: #fff !important; fill: #fff !important; }
    .flatpickr-weekdays { background: #1f2937 !important; }
    .flatpickr-weekday { color: #9ca3af !important; }
    .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month { background-color: #1f2937 !important; }
</style>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/tr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.umd.min.js"></script>
<script>
    // --- Flatpickr Initialization ---
    flatpickr(".datepicker", {
        locale: "tr",
        dateFormat: "Y-m-d",
        theme: "dark",
        allowInput: true
    });

    // --- CountUp.js Initialization ---
    document.addEventListener('DOMContentLoaded', function() {
        const options = { duration: 2.5, useEasing: true, useGrouping: true };
        
        // Helper to init countUp safely
        const initCounter = (id, decimalPlaces = 0) => {
            const el = document.getElementById(id);
            if(el) {
                const val = parseFloat(el.innerText.replace(',', '.')); // Handle locale
                // Re-format clean for animation start if needed, but CountUp takes endVal
                const anim = new countUp.CountUp(id, val, { ...options, decimalPlaces });
                if (!anim.error) anim.start();
            }
        };

        // Delay slightly for visual effect after Alpine transition
        setTimeout(() => {
            initCounter('counter-total-work', 1);
            initCounter('counter-efficiency', 2);
            initCounter('counter-personnel', 0);
            initCounter('counter-overtime', 2);
        }, 500);
    });

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
    const weeklyRhythmData = {!! json_encode(array_values($weeklyRhythm)) !!};
    const rhythmCtx = document.getElementById('weeklyRhythmChart').getContext('2d');
    new Chart(rhythmCtx, {
        type: 'bar',
        data: {
            labels: weeklyRhythmData.map(d => d.day),
            datasets: [{
                label: 'Ortalama İş Süresi (Saat)',
                data: weeklyRhythmData.map(d => d.avg_hours),
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
