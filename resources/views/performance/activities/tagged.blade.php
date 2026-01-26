@extends('layouts.master')

@section('title', 'Taglenmiş Aktiviteler')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Taglenmiş Aktiviteler</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kategorize edilmiş kullanıcı aktiviteleri</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('activities.index') }}" class="text-primary-600 hover:text-primary-700">Aktiviteler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Taglenmiş</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    Taglenmiş Aktivite Listesi
                </h5>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('activities.index') }}" class="btn btn-secondary text-sm">
                    <i class="fas fa-list mr-1"></i> Tümü
                </a>
                <a href="{{ route('activities.untagged') }}" class="btn btn-warning text-sm text-white">
                    <i class="fas fa-exclamation-circle mr-1"></i> Taglenmemiş
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'username', 'sort_order' => request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center">
                                    Kullanıcı
                                    @if(request('sort_by') == 'username')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-300 ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'process_name', 'sort_order' => request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center">
                                    Process Name
                                    @if(request('sort_by') == 'process_name')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-300 ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center">
                                    Başlık
                                    @if(request('sort_by') == 'title')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-300 ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategoriler</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Güven Skoru</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tip</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'duration_ms', 'sort_order' => request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center">
                                    Süre
                                    @if(request('sort_by') == 'duration_ms')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-300 ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'start_time_utc', 'sort_order' => request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center">
                                    Zaman
                                    @if(request('sort_by') == 'start_time_utc' || !request('sort_by'))
                                        <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-300 ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Detay</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($activities as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    @php
                                        $displayName = $activity->computer_user_display_name ?? $activity->username;
                                        $displayInitial = substr($displayName, 0, 2);
                                    @endphp
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-xs" title="{{ $activity->username }}">
                                        {{ $displayInitial }}
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" title="{{ $activity->username }}">
                                            {{ $displayName }}
                                        </div>
                                        @if($activity->computer_user_hostname)
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                                <i class="fas fa-desktop text-[9px]"></i>
                                                {{ $activity->computer_user_hostname }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white font-mono bg-gray-100 dark:bg-gray-800 rounded px-2 py-1 inline-block">
                                    {{ Str::limit($activity->process_name, 25) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300" title="{{ $activity->title }}">
                                    {{ Str::limit($activity->title, 40) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($activity->categories as $category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $avgScore = $activity->categories->avg('pivot.confidence_score') ?? 0; @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full {{ $avgScore >= 80 ? 'bg-green-500' : ($avgScore >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                             style="width: {{ $avgScore }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ $avgScore >= 80 ? 'text-green-600' : ($avgScore >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        %{{ number_format($avgScore, 0) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $isManual = $activity->categories->first()?->pivot->is_manual ?? false; @endphp
                                @if($isManual)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded text-xs font-medium">
                                        <i class="fas fa-hand-pointer text-xs"></i> Manuel
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded text-xs font-medium">
                                        <i class="fas fa-magic text-xs"></i> Otomatik
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded font-medium text-xs">
                                    <i class="fas fa-clock text-xs"></i>
                                    {{ $activity->duration_formatted }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $activity->start_time_utc->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick='openModal(@json($activity))' class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 bg-primary-50 dark:bg-primary-900/20 px-3 py-1 rounded transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i> Detay
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 flex justify-center">
                {{ $activities->links() }}
            </div>
        </div>
    </div>
</div>
@include('performance.activities.partials.detail-modal')
@endsection
