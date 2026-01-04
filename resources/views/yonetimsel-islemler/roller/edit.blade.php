@extends('layouts.master')
@section('title', 'Rol Güncelle')

@section('css')
@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Rol Güncelle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Rol ismini düzenleyin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('roller.index') }}" class="text-primary-600 hover:text-primary-700">Rol İşlemleri</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Rol Güncelle</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-edit text-primary-500"></i>
            Rol Güncelle
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('roller.update', $roller->id) }}" method="POST" class="max-w-xl">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <x-input 
                    label="Rol Adı" 
                    name="name" 
                    id="name"
                    :value="$roller->name"
                    :error="$errors->first('name')"
                />
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('roller.index') }}" class="btn btn-secondary">Vazgeç</a>
                <button type="submit" class="btn btn-primary">Güncelle</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')

@endsection
