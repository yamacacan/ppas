@extends('layouts.master')

@section('title', 'Firma Ayarları')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Firma Ayarları</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tüm aktiviteleriniz ve uyarılarınız</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Firma Ayarları</span>
    </li>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Firma Ayarları ve Mesai Saatleri</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('firm-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h5 class="text-lg font-semibold text-primary-600 dark:text-primary-400 mb-4">Firma Bilgileri</h5>
                            
                            <x-input 
                                label="Firma Adı" 
                                name="firm_name" 
                                id="firm_name"
                                :value="old('firm_name', $settings->firm_name)"
                            />

                            <x-input 
                                type="email"
                                label="E-posta Adresi" 
                                name="email" 
                                id="email"
                                :value="old('email', $settings->email)"
                            />

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Adres</label>
                                <textarea 
                                    id="address" 
                                    name="address" 
                                    rows="3"
                                    class="w-full px-4 py-2.5 rounded-lg border bg-white dark:bg-gray-800 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 dark:focus:ring-primary-900/30 outline-none transition-all duration-200"
                                >{{ old('address', $settings->address) }}</textarea>
                            </div>

                            <div>
                                <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Firma Logosu</label>
                                <input 
                                    type="file" 
                                    id="logo" 
                                    name="logo"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                                >
                                @if($settings->logo_path)
                                    <div class="mt-3 p-2 border border-gray-200 dark:border-gray-700 rounded-lg inline-block bg-white dark:bg-gray-800">
                                        <p class="text-xs text-gray-500 mb-1">Mevcut Logo:</p>
                                        <img src="{{ Storage::url($settings->logo_path) }}" alt="Firma Logosu" class="max-h-24 rounded">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h5 class="text-lg font-semibold text-primary-600 dark:text-primary-400 mb-4">Mesai Saatleri</h5>
                            <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 p-4 rounded-lg text-sm mb-4 border border-blue-100 dark:border-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Bu saatler performans ölçümlerinde "Mesai İçi" ve "Mesai Dışı" hesaplamalarında kullanılacaktır.
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <x-input 
                                    type="time"
                                    label="Mesai Başlangıç Saati" 
                                    name="work_start_time" 
                                    id="work_start_time"
                                    :value="old('work_start_time', \Carbon\Carbon::parse($settings->work_start_time)->format('H:i'))"
                                    required
                                />

                                <x-input 
                                    type="time"
                                    label="Mesai Bitiş Saati" 
                                    name="work_end_time" 
                                    id="work_end_time"
                                    :value="old('work_end_time', \Carbon\Carbon::parse($settings->work_end_time)->format('H:i'))"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
