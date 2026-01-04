@extends('layouts.master')
@section('title', 'Yeni Birim Ekle')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Birim Ekle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organizasyona yeni bir birim tanımlayın</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('birim.index') }}" class="text-primary-600 hover:text-primary-700">Birim İşlemleri</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Birim Ekle</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-folder-plus text-primary-500"></i>
            Birim Bilgileri
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('birim.store') }}" method="POST">
            @csrf
            
            @php
                $unitOptions = $units->pluck('name', 'id');
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Parent Unit -->
                <x-select 
                    label="Bağlı Olduğu Birim" 
                    name="parent_id" 
                    id="parent_id"
                    :options="$unitOptions"
                    placeholder="Seçiniz (Opsiyonel)"
                    searchable="true"
                />

                <!-- Unit Name -->
                <x-input 
                    label="Birim Adı" 
                    name="name" 
                    id="name"
                    required
                    :error="$errors->first('name')"
                    placeholder="Birim adını giriniz"
                />
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('birim.index') }}" class="btn btn-secondary">Vazgeç</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection