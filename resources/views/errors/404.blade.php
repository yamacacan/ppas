@extends('layouts.errors.master')
@section('title', 'Not Found')

@section('content')
<div class="max-w-lg w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden text-center p-8">
    <div class="mb-6 flex justify-center">
        <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center animate-pulse">
            <span class="text-4xl font-bold text-blue-600 dark:text-blue-400">404</span>
        </div>
    </div>
    
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Sayfa Bulunamadı</h2>
    
    <p class="text-gray-600 dark:text-gray-300 mb-8">
        Aradığınız sayfa silinmiş, adı değiştirilmiş veya geçici olarak kullanılamıyor olabilir. Lütfen URL'yi kontrol ediniz.
    </p>
    
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors duration-200">
        <i class="fas fa-home mr-2"></i>
        Ana Sayfaya Dön
    </a>
</div>
@endsection
