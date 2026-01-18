@extends('layouts.master')

@section('title', 'Kullanıcı Düzenle')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kullanıcı Düzenle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kullanıcı profilini ve birim atamasını güncelleyin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('computer-users.index') }}" class="text-primary-600 hover:text-primary-700">Kullanıcılar</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Düzenle</span>
    </li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Edit Form -->
    <div class="lg:col-span-2">
        <form action="{{ route('computer-users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-header border-b border-gray-200 dark:border-gray-700">
                    <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-user-edit text-primary-500"></i>
                        Profil Bilgileri
                    </h5>
                </div>
                
                <div class="card-body space-y-6">
                    <!-- Read-only Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Kullanıcı Adı (Sistem)
                            </label>
                            <div class="relative">
                                <input type="text" value="{{ $user->username }}" 
                                       class="form-input bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed pl-10" 
                                       disabled>
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Agent tarafından otomatik toplanır, değiştirilemez.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Anakart UUID
                            </label>
                            <div class="relative">
                                <input type="text" value="{{ $user->motherboard_uuid }}" 
                                       class="form-input bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed pl-10" 
                                       disabled>
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <i class="fas fa-microchip text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>

                    <!-- Editable Fields -->
                    <div>
                        <x-input 
                            label="Ad Soyad / Görünen İsim" 
                            name="name" 
                            id="name"
                            :value="old('name', $user->name)"
                            placeholder="Örn: Yamaç A."
                            :error="$errors->first('name')"
                        />
                        <p class="mt-1 text-xs text-gray-500">Raporlarda görünecek isim</p>
                    </div>

                    <div>
                        @php
                            $unitOptions = ['' => 'Birim Seçiniz...'];
                            foreach($units as $unit) {
                                $unitOptions[$unit->id] = $unit->name;
                            }
                        @endphp
                        <x-select 
                            label="Birim" 
                            name="unit_id" 
                            id="unit_id"
                            :options="$unitOptions"
                            :value="old('unit_id', $user->unit_id)"
                            :error="$errors->first('unit_id')"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Kullanıcının birimi, keyword sınıflandırmalarındaki istisnalar için kullanılır.
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('computer-users.index') }}" class="btn btn-secondary">
                            İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i> Değişiklikleri Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Sidebar -->
    <div class="lg:col-span-1">
        <div class="card bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800">
            <div class="card-body">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold text-xl">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="font-bold text-gray-900 dark:text-white">{{ $user->username }}</h6>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Son görülme: {{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm border-b border-blue-200 dark:border-blue-800 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Kayıt Tarihi</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-blue-200 dark:border-blue-800 pb-2">
                        <span class="text-gray-600 dark:text-gray-400">Toplam Aktivite</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ number_format($user->activities()->count()) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
