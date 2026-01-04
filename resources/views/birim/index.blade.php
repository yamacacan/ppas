@extends('layouts.master')
@section('title', 'Birimler')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Birimler</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organizasyonel birim yapısını yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Birim İşlemleri</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Birimler</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-sitemap text-primary-500"></i>
                    Birim Listesi
                </h5>
            </div>
            <a href="{{ route('birim.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Birim Ekle
            </a>
        </div>
    </div>

    <div class="card-body">
        @if ($message = session('message'))
            <div class="alert alert-success mb-6 flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                <i class="fas fa-check-circle"></i>
                {{ $message }}
            </div>
        @elseif($message = session('error'))
            <div class="alert alert-danger mb-6 flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 rounded-lg p-4 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                <i class="fas fa-exclamation-circle"></i>
                {{ $message }}
            </div>
        @endif

        @php
            // Prepare data for the data-table component
            $tableRows = $units->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'parent_name' => $unit->parentUnit ? $unit->parentUnit->name : 'Merkez',
                    'name' => $unit->name,
                    'can_edit' => $unit->id != 1,
                    'can_delete' => $unit->id != 1,
                ];
            });
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Bağlı Olduğu Birim', 'key' => 'parent_name'],
                ['header' => 'Birim Adı', 'key' => 'name']
            ]" 
            :rows="$tableRows" 
            edit-route="birim.edit" 
            delete-route="birim.destroy" 
        />
    </div>
</div>
@endsection
