@extends('layouts.master')

@section('title', 'Aktivite Yönetimi')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Aktivite Yönetimi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tüm kullanıcı aktivitelerini görüntüleyin ve yönetin</p>
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
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($activities->total()) }}</h3>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Başlangıç Tarihi</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bitiş Tarihi</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                class="form-input w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durum</label>
                            <select name="status" class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Hepsi</option>
                                <option value="tagged" {{ request('status') == 'tagged' ? 'selected' : '' }}>Taglenmiş</option>
                                <option value="untagged" {{ request('status') == 'untagged' ? 'selected' : '' }}>Taglenmemiş</option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                            <select name="category_id" class="form-select w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Tüm Kategoriler</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Text Searches -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kullanıcı Adı</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <i class="fas fa-user text-xs"></i>
                                </span>
                                <input type="text" name="username" value="{{ request('username') }}" placeholder="Kullanıcı ara..."
                                    class="form-input w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Process</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <i class="fas fa-cog text-xs"></i>
                                </span>
                                <input type="text" name="process" value="{{ request('process') }}" placeholder="Process ara..."
                                    class="form-input w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Başlık</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                                    <i class="fas fa-heading text-xs"></i>
                                </span>
                                <input type="text" name="title" value="{{ request('title') }}" placeholder="Başlık ara..."
                                    class="form-input w-full pl-8 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-primary-500 focus:ring-primary-500">
                            </div>
                        </div>
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

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table" id="activitiesTable">
                <thead>
                    <tr>
                        <th>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user text-gray-400"></i>
                                Kullanıcı
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-cog text-gray-400"></i>
                                Process
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-alt text-gray-400"></i>
                                Başlık
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tags text-gray-400"></i>
                                Kategoriler
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-clock text-gray-400"></i>
                                Başlangıç
                            </div>
                        </th>
                        <th>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-hourglass-half text-gray-400"></i>
                                Süre
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-600 dark:text-primary-400 text-xs"></i>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $activity->username }}</span>
                            </div>
                        </td>
                        <td>
                            <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 text-sm font-mono rounded border border-gray-200 dark:border-gray-700">
                                {{ Str::limit($activity->process_name, 30) }}
                            </code>
                        </td>
                        <td>
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ Str::limit($activity->title, 50) }}</span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @forelse($activity->categories as $category)
                                    <span class="badge badge-primary">{{ $category->name }}</span>
                                @empty
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-ban text-xs mr-1"></i> Taglenmemiş
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $activity->start_time_utc->format('d.m.Y') }}</div>
                                <div class="text-gray-500 dark:text-gray-400">{{ $activity->start_time_utc->format('H:i:s') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded font-medium text-sm">
                                <i class="fas fa-stopwatch text-xs"></i>
                                {{ $activity->duration_formatted }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <i class="fas fa-inbox text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Aktivite bulunamadı</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-sm text-blue-700 dark:text-blue-300">
                    <div>
                        <strong>Taglenmiş:</strong> En az bir kategoriye atanmış aktiviteler
                    </div>
                    <div>
                        <strong>Taglenmemiş:</strong> Henüz kategorize edilmemiş aktiviteler
                    </div>
                    <div>
                        <strong>Otomatik Tagleme:</strong> Keyword'lere göre toplu etiketleme
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#activitiesTable').DataTable({
            "pageLength": 50,
            "order": [[4, 'desc']], // Başlangıç zamanına göre sırala
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
            },
            "dom": '<"flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4"lf>rtip',
            "responsive": true
        });
    });
</script>
@endsection
