@extends('layouts.master')

@section('title', 'Aktivite Detayo')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Aktivite Detayı</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aktivite ve süreç detaylarını görüntüleyin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('activities.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary-600">Aktiviteler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Detay</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-info-circle text-primary-500"></i>
            Aktivite Bilgileri
        </h5>
        <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Listeye Dön
        </a>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sol Kolon: Temel Bilgiler -->
            <div class="space-y-6">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Kullanıcı</label>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-primary-600 dark:text-primary-400 text-sm"></i>
                        </div>
                        <span class="font-medium text-lg text-gray-900 dark:text-white">{{ $activity->username }}</span>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Process</label>
                    <code class="px-3 py-2 bg-gray-100 dark:bg-gray-800 text-base font-mono rounded-lg border border-gray-200 dark:border-gray-700 block w-full text-gray-800 dark:text-gray-200">
                        {{ $activity->process_name }}
                    </code>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Pencere Başlığı</label>
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-200">
                        {{ $activity->title }}
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon: Zaman ve Kategoriler -->
            <div class="space-y-6">
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <h6 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="far fa-clock text-gray-400"></i> Zaman Bilgileri
                    </h6>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500 block mb-1">Başlangıç</span>
                            <span class="font-medium text-gray-900 dark:text-white text-lg">
                                {{ $activity->start_time_utc ? $activity->start_time_utc->format('H:i:s') : '-' }}
                            </span>
                            <span class="text-xs text-gray-400 block">{{ $activity->start_time_utc ? $activity->start_time_utc->format('d.m.Y') : '' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 block mb-1">Süre</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-md font-bold text-lg">
                                {{ $activity->duration_formatted }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Kategoriler</label>
                    <div class="flex flex-wrap gap-2">
                        @forelse($activity->categories as $category)
                            <span class="badge badge-primary px-3 py-1.5 text-sm flex items-center gap-1">
                                <i class="fas fa-tag text-xs opacity-70"></i>
                                {{ $category->name }}
                            </span>
                        @empty
                            <span class="badge badge-secondary px-3 py-1.5 text-sm">
                                <i class="fas fa-ban text-xs mr-1"></i> Kategorize Edilmemiş
                            </span>
                        @endforelse
                    </div>
                    @if($activity->categories->isEmpty())
                        <p class="text-xs text-gray-400 mt-2">Bu aktivite için henüz bir kategori atanmamış.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
