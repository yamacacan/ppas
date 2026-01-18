@extends('layouts.master')
@section('title', 'Ünvanlar')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Ünvanlar</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Personel ünvanlarını yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Ünvan İşlemleri</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Ünvanlar</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-id-badge text-primary-500"></i>
                    Ünvan Listesi
                </h5>
            </div>
            <a href="{{ route('unvan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Ünvan Ekle
            </a>
        </div>
    </div>

    <div class="card-body">
        @php
            $tableRows = $titles->map(function($title, $index) {
                return [
                    'id' => $title->id,
                    'index' => $index + 1,
                    'name' => '<div class="font-medium text-gray-900 dark:text-white">'.$title->name.'</div>',
                ];
            });
        @endphp

        <x-data-table 
            :columns="[
                ['header' => '#', 'key' => 'index'],
                ['header' => 'Ünvan Adı', 'key' => 'name'],
            ]" 
            :rows="$tableRows" 
            edit-route="unvan.edit" 
            delete-route="unvan.destroy" 
        />
    </div>
</div>
@endsection