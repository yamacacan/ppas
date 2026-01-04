@extends('layouts.master')

@section('title', 'Aktivite Yönetimi')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Aktivite Yönetimi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tüm kullanıcı aktivitelerini izleyin ve yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Aktiviteler</span>
    </li>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Toplam</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($activities instanceof \Illuminate\Pagination\LengthAwarePaginator ? $activities->total() : $activities->count()) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">aktivite</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Taglenmiş</p>
                    <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($taggedCount) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">kategorize edilmiş</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Taglenmemiş</p>
                    <h3 class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($untaggedCount) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">bekliyor</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Filtreli Gösterim</p>
                    <h3 class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $activities->count() }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">mevcut sayfa</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-primary-600 dark:text-primary-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-tasks text-primary-500"></i>
                    Aktivite Listesi
                </h5>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tüm kullanıcı aktivitelerini görüntüleyin</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('activities.untagged') }}" class="btn btn-sm bg-orange-500 text-white hover:bg-orange-600">
                    <i class="fas fa-exclamation-circle mr-1"></i> Taglenmemiş
                </a>
                <a href="{{ route('activities.tagged') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-check-circle mr-1"></i> Taglenmiş
                </a>
                <a href="{{ route('activities.auto-tag') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-magic mr-1"></i> Otomatik Tagleme
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Advanced Filters -->
        <div class="mb-6">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <form method="GET" action="{{ route('activities.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Date Range -->
                        <div class="w-full">
                            <x-datepicker 
                                label="Başlangıç Tarihi" 
                                name="start_date" 
                                id="start_date"
                                value="{{ request('start_date') }}"
                                placeholder="Seçiniz"
                            />
                        </div>
                        <div class="w-full">
                            <x-datepicker 
                                label="Bitiş Tarihi" 
                                name="end_date" 
                                id="end_date"
                                value="{{ request('end_date') }}"
                                placeholder="Seçiniz"
                            />
                        </div>

                        <!-- Status Filter -->
                        <x-select 
                            label="Durum" 
                            name="status" 
                            id="status"
                            :options="[
                                'all' => 'Hepsi',
                                'tagged' => 'Taglenmiş',
                                'untagged' => 'Taglenmemiş'
                            ]"
                            :value="request('status', 'all')"
                        />

                        <!-- Category Filter -->
                        @php
                            $categoryOptions = ['' => 'Tüm Kategoriler'];
                            foreach($categories as $category) {
                                $categoryOptions[$category->id] = $category->name;
                            }
                        @endphp
                        <x-select 
                            label="Kategori" 
                            name="category_id" 
                            id="category_id"
                            :options="$categoryOptions"
                            :value="request('category_id')"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Text Searches -->
                        <x-input 
                            label="Kullanıcı Adı" 
                            name="username" 
                            id="username"
                            value="{{ request('username') }}"
                            placeholder="Kullanıcı ara..."
                            icon="fas fa-user"
                        />
                        <x-input 
                            label="Process" 
                            name="process" 
                            id="process"
                            value="{{ request('process') }}"
                            placeholder="Process ara..."
                            icon="fas fa-cog"
                        />
                        <x-input 
                            label="Başlık" 
                            name="title" 
                            id="title"
                            value="{{ request('title') }}"
                            placeholder="Başlık ara..."
                            icon="fas fa-heading"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        @if(request()->anyFilled(['category_id', 'username', 'process', 'title', 'status', 'start_date', 'end_date']))
                            <a href="{{ route('activities.index') }}" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                                <i class="fas fa-times mr-1"></i> Temizle
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i> Filtrele
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tailwind Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="max-h-[800px] overflow-y-auto"> <!-- Yükseklik sınırlandırması ve dikey kaydırma -->
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10"> <!-- Sticky header -->
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
                                    Process
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Kategoriler
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'start_time_utc', 'sort_order' => request('sort_order', 'desc') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex items-center">
                                    Başlangıç
                                    @if(request('sort_by') == 'start_time_utc' || !request('sort_by'))
                                        <i class="fas fa-sort-{{ request('sort_order', 'desc') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort text-gray-300 ml-1 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    @endif
                                </a>
                            </th>
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
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Detay</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-xs">
                                        {{ substr($activity->username, 0, 2) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity->username }}
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
                                    {{ Str::limit($activity->title, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($activity->categories as $category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $category->name }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                            Taglenmemiş
                                        </span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $activity->start_time_utc->format('d.m.Y') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $activity->start_time_utc->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $activity->duration_formatted }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick='openModal(@json($activity))' class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 bg-primary-50 dark:bg-primary-900/20 px-3 py-1 rounded transition-colors duration-200">
                                    <i class="fas fa-eye mr-1"></i> Detay
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-900 dark:text-white">Aktivite Bulunamadı</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Arama kriterlerinize uygun kayıt bulunamadı.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 flex justify-center">
                {{ $activities->links('pagination.custom') }}
            </div>
        </div>
        
        <div class="mt-4 text-xs text-gray-500 text-right">
            Toplam {{ number_format($activities->total()) }} kayıt listelendi.
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="card mt-6 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
    <div class="card-body">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info text-white"></i>
            </div>
            <div>
                <h6 class="font-bold text-blue-900 dark:text-blue-300 mb-2">Aktivite Takibi Hakkında</h6>
                <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                    Bu sayfada tüm kullanıcı aktiviteleri listelenir. Aktiviteler otomatik olarak kaydedilir ve keyword'lere göre kategorize edilir.
                    <br>Veriler çok fazla olduğunda dikey kaydırma (scroll) aktif olur. "Detay" butonuna tıklayarak aktivite hakkında daha fazla bilgi alabilirsiniz.
                </p>
            </div>
        </div>
    </div>
</div>

@include('performance.activities.partials.detail-modal')
@endsection
