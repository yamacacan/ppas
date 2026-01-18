@extends('layouts.master')
@section('title', 'Yeni Ünvan Ekle')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Ünvan Ekle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sisteme yeni bir ünvan tanımlayın</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('unvan.index') }}" class="text-primary-600 hover:text-primary-700">Ünvan İşlemleri</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Ünvan Ekle</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-plus-circle text-primary-500"></i>
            Ünvan Ekle
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('unvan.store') }}" method="POST" class="max-w-xl">
            @csrf
            
            <div class="mb-6">
                <x-input 
                    label="Ünvan Adı" 
                    name="name" 
                    id="name" 
                    placeholder="Örn: Yazılım Uzmanı" 
                    required="true"
                />
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('unvan.index') }}" class="btn btn-secondary">Vazgeç</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection