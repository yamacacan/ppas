@extends('layouts.master')

@section('title', 'Kullanıcılar')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bilgisayar Kullanıcıları</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki kullanıcıları ve birimlerini yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Kullanıcılar</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-users text-primary-500"></i>
                Kullanıcı Listesi
            </h5>
            <div class="text-sm text-gray-500">
                Toplam <span class="font-bold text-gray-900 dark:text-white">{{ $users->count() }}</span> kullanıcı
            </div>
        </div>
    </div>
    
    <div class="card-body">
        @php
            $tableRows = $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'username' => '
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                                <i class="fas fa-user-lock"></i>
                            </div>
                            <code class="text-sm text-pink-600 dark:text-pink-400 font-mono bg-gray-100 dark:bg-gray-800/50 px-2 py-0.5 rounded">
                                '.$user->username.'
                            </code>
                        </div>',
                    'name' => $user->name 
                        ? '<span class="font-medium text-gray-900 dark:text-white">'.$user->name.'</span>'
                        : '<span class="text-sm text-gray-400 italic">Tanımsız</span>',
                    'unit' => $user->unit 
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"><i class="fas fa-building mr-1"></i> '.$user->unit->name.'</span>'
                        : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Global</span>',
                    'system_info' => '
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                '.($user->hostname ?? 'Bilinmeyen PC').'
                            </span>
                            <span class="text-[10px] text-gray-400 mt-1" title="'.$user->motherboard_uuid.'">
                                UUID: '.Str::limit($user->motherboard_uuid, 12).'
                            </span>
                        </div>',
                    'total_duration' => '<span class="text-sm font-mono text-gray-900 dark:text-white">'.number_format(($user->activities_sum_duration_ms ?? 0) / (1000 * 60 * 60), 1).' Saat</span>',
                    'actions' => '
                        <div class="flex items-center justify-end gap-2">
                            <a href="'.route('computer-users.show', $user->id).'" 
                               class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all"
                               title="İstatistikler">
                                <i class="fas fa-chart-pie"></i>
                            </a>
                            <a href="'.route('computer-users.edit', $user->id).'" 
                               class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all"
                               title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>'
                ];
            });
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Kullanıcı Adı', 'key' => 'username'],
                ['header' => 'Görünen İsim', 'key' => 'name'],
                ['header' => 'Birim', 'key' => 'unit'],
                ['header' => 'Sistem Bilgisi', 'key' => 'system_info'],
                ['header' => 'Toplam Süre', 'key' => 'total_duration'],
                ['header' => '', 'key' => 'actions']
            ]" 
            :rows="$tableRows" 
        />
    </div>
</div>
@endsection
