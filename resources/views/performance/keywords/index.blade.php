@extends('layouts.master')

@section('title', 'Keyword Yönetimi')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Keyword Yönetimi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aktivite eşleştirme keyword'leri</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Keyword'ler</span>
    </li>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Toplam Keyword</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $keywords->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Aktif</p>
                    <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $keywords->where('is_active', true)->count() }}</h3>
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
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pasif</p>
                    <h3 class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $keywords->where('is_active', false)->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-gray-600 dark:text-gray-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Regex</p>
                    <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $keywords->where('match_type', 'regex')->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-code text-purple-600 dark:text-purple-400 text-xl"></i>
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
                    <i class="fas fa-list text-primary-500"></i>
                    Keyword Listesi
                </h5>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tüm keyword'leri yönetin ve düzenleyin</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('keywords.test') }}" class="btn btn-sm bg-cyan-500 text-white hover:bg-cyan-600">
                    <i class="fas fa-flask mr-1"></i> Keyword Test
                </a>
                <a href="{{ route('keywords.import') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-upload mr-1"></i> Toplu İçe Aktar
                </a>
                <a href="{{ route('keywords.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Yeni Keyword
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @php
            $tableRows = $keywords->map(function($keyword) {
                 $matchTypeBadge = '';
                 switch($keyword->match_type) {
                    case 'exact': $matchTypeBadge = '<span class="badge bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400"><i class="fas fa-equals text-xs mr-1"></i> Tam Eşleşme</span>'; break;
                    case 'contains': $matchTypeBadge = '<span class="badge bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"><i class="fas fa-search text-xs mr-1"></i> İçeriyor</span>'; break;
                    case 'starts_with': $matchTypeBadge = '<span class="badge bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"><i class="fas fa-arrow-right text-xs mr-1"></i> Başlıyor</span>'; break;
                    case 'ends_with': $matchTypeBadge = '<span class="badge bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400"><i class="fas fa-arrow-left text-xs mr-1"></i> Bitiyor</span>'; break;
                    case 'regex': $matchTypeBadge = '<span class="badge bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400"><i class="fas fa-code text-xs mr-1"></i> Regex</span>'; break;
                 }

                 $categoryBadge = $keyword->category 
                    ? '<span class="badge badge-primary">'.$keyword->category->name.'</span>'
                    : '<span class="badge badge-warning custom-warning-badge text-yellow-800 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30">Kategorisiz</span>';

                 $statusBadge = $keyword->is_active 
                    ? '<span class="badge badge-success"><i class="fas fa-check text-xs mr-1"></i> Aktif</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times text-xs mr-1"></i> Pasif</span>';

                 return [
                    'id' => $keyword->id,
                    'keyword' => '
                        <code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 text-primary-600 dark:text-primary-400 rounded text-sm font-mono border border-gray-200 dark:border-gray-700">
                            '.$keyword->keyword.'
                        </code>',
                    'category' => $categoryBadge,
                    'match_type' => $matchTypeBadge,
                    'priority' => '
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden w-24">
                                <div class="h-full bg-primary-500 rounded-full" style="width: '.($keyword->priority * 10).'%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 w-8">'.$keyword->priority.'</span>
                        </div>',
                    'case_sensitive' => $keyword->is_case_sensitive 
                        ? '<span class="badge bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"><i class="fas fa-font text-xs mr-1"></i> Evet</span>'
                        : '<span class="badge badge-secondary"><i class="fas fa-font text-xs mr-1"></i> Hayır</span>',
                    'status' => $statusBadge
                 ];
            });
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Keyword', 'key' => 'keyword'],
                ['header' => 'Kategori', 'key' => 'category'],
                ['header' => 'Eşleşme Tipi', 'key' => 'match_type'],
                ['header' => 'Öncelik', 'key' => 'priority'],
                ['header' => 'Case Sensitive', 'key' => 'case_sensitive'],
                ['header' => 'Durum', 'key' => 'status']
            ]" 
            :rows="$tableRows" 
            edit-route="keywords.edit" 
            delete-route="keywords.destroy" 
        />
    </div>
</div>

<!-- Info Card -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <div class="card bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
        <div class="card-body">
            <h6 class="font-bold text-blue-900 dark:text-blue-300 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                Eşleşme Tipleri Hakkında
            </h6>
            <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                <p><strong>Tam Eşleşme:</strong> Keyword ile metin tam olarak aynı olmalı</p>
                <p><strong>İçeriyor:</strong> Metin içinde keyword geçmeli</p>
                <p><strong>Başlıyor:</strong> Metin keyword ile başlamalı</p>
                <p><strong>Bitiyor:</strong> Metin keyword ile bitmeli</p>
                <p><strong>Regex:</strong> Düzenli ifade ile eşleştirme</p>
            </div>
        </div>
    </div>

    <div class="card bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800">
        <div class="card-body">
            <h6 class="font-bold text-purple-900 dark:text-purple-300 mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb"></i>
                Öncelik Sistemi
            </h6>
            <div class="space-y-2 text-sm text-purple-800 dark:text-purple-200">
                <p><strong>1-3:</strong> Düşük öncelik - Genel eşleştirmeler</p>
                <p><strong>4-6:</strong> Orta öncelik - Standart keyword'ler</p>
                <p><strong>7-10:</strong> Yüksek öncelik - Özel eşleştirmeler</p>
                <p class="text-xs mt-2 text-purple-600 dark:text-purple-400">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Yüksek öncelikli keyword'ler önce kontrol edilir
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
