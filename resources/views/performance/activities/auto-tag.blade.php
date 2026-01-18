@extends('layouts.master')

@section('title', 'Otomatik Tagleme')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Otomatik Tagleme</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Keyword kurallarına göre toplu etiketleme</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('activities.auto-tag') }}" class="text-primary-600 hover:text-primary-700">Otomatik Tagleme</a>
    </li>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat Cards -->
    <div class="card bg-blue-50 dark:bg-blue-900/10 border-l-4 border-blue-500">
        <div class="card-body p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Toplam Aktivite</p>
                    <h2 class="text-3xl font-bold text-blue-800 dark:text-blue-200 mt-2">{{ number_format($totalCount) }}</h2>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-database text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-green-50 dark:bg-green-900/10 border-l-4 border-green-500">
        <div class="card-body p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Taglenmiş</p>
                    <h2 class="text-3xl font-bold text-green-800 dark:text-green-200 mt-2">{{ number_format($taggedCount) }}</h2>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-orange-50 dark:bg-orange-900/10 border-l-4 border-orange-500">
        <div class="card-body p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Taglenmemiş</p>
                    <h2 class="text-3xl font-bold text-orange-800 dark:text-orange-200 mt-2">{{ number_format($untaggedCount) }}</h2>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-orange-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Action Card -->
    <div class="card h-full">
        <div class="card-header border-b border-gray-200 dark:border-gray-700">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-magic text-primary-500"></i>
                Otomatik Tagleme Başlat
            </h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="p-4 mb-6 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-2 text-xl"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($untaggedCount > 0)
                <form action="{{ route('activities.auto-tag.run') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            İşlenecek Aktivite Sayısı
                        </label>
                        <div class="relative">
                            <input type="number" name="limit" 
                                   class="form-input text-lg font-medium pl-10" 
                                   value="100" min="1" max="1000">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i class="fas fa-sort-numeric-up text-gray-400"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Performans için tek seferde en fazla 1000 aktivite işlenebilir.
                        </p>
                    </div>

                    <button type="submit" class="btn btn-primary w-full py-3 text-lg shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
                        <i class="fas fa-rocket mr-2"></i>
                        Tagleme İşlemini Başlat
                    </button>
                </form>
            @else
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                        <i class="fas fa-check text-4xl text-green-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Her Şey Güncel!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">
                        İşlenecek taglenmemiş aktivite bulunmuyor.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Card -->
    <div class="card h-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        <div class="card-body p-8">
            <h5 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Nasıl Çalışır?
            </h5>
            
            <ul class="space-y-5">
                <li class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-blue-600 font-bold text-sm">1</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">Veri Analizi</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Sistem, taglenmemiş aktivitelerin <code>process_name</code>, <code>title</code> ve <code>url</code> alanlarını tarar.
                        </p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-blue-600 font-bold text-sm">2</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">Keyword Eşleştirme</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Tanımlı keyword'ler ile eşleşme aranır. (Tam eşleşme, içerir, başlar, biter vb.)
                        </p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-blue-600 font-bold text-sm">3</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">Skorlama & Atama</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Eşleşme tipine göre güven skoru hesaplanır (örn. Tam Eşleşme = 100). En yüksek skora sahip kategori atanır.
                        </p>
                    </div>
                </li>
                <li class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <span class="text-blue-600 font-bold text-sm">4</span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">İstisnalar</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Eğer kullanıcı veya birim bazlı istisna tanımlıysa, kategori buna göre override edilir.
                        </p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
