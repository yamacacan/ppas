@extends('layouts.master')

@section('title', 'Birim İstatistikleri')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Birim İstatistikleri</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Departman bazlı performans analizleri</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Birimler</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-building text-primary-500"></i>
            Birim Listesi
        </h5>
    </div>
    <div class="card-body">
        @php
            $tableRows = $units->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'name' => '
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sitemap text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <span class="font-medium text-gray-900 dark:text-white">'.$unit->name.'</span>
                        </div>',
                    'user_count' => '
                        <span class="badge badge-light-primary">
                            <i class="fas fa-users mr-1"></i> '.$unit->computer_users_count.'
                        </span>',
                    'activity_count' => '<span class="text-gray-700 dark:text-gray-300">'.number_format($unit->activity_count).'</span>',
                    'duration' => '<span class="text-gray-700 dark:text-gray-300 font-medium">'.$unit->total_duration_hours.'s</span>',
                    'actions' => '
                        <div class="flex justify-end">
                            <a href="'.route('unit-statistics.show', $unit->id).'" 
                               class="inline-flex items-center gap-1 px-3 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all">
                                <i class="fas fa-chart-pie"></i> <span>Detaylar</span>
                            </a>
                        </div>'
                ];
            });
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Birim Adı', 'key' => 'name'],
                ['header' => 'Kullanıcı Sayısı', 'key' => 'user_count'],
                ['header' => 'Toplam Aktivite', 'key' => 'activity_count'],
                ['header' => 'Toplam Süre (Saat)', 'key' => 'duration'],
                ['header' => '', 'key' => 'actions']
            ]" 
            :rows="$tableRows" 
        />
    </div>
</div>
@endsection
