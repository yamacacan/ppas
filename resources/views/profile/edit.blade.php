@extends('layouts.master')
@section('title', 'Profilim')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Profilim</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Profil bilgilerinizi ve şifrenizi buradan güncelleyebilirsiniz.</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Profilim</span>
    </li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-xl-12">
            <div class="card shadow-sm rounded-lg">
                <div class="flex items-center justify-between card-header pb-5px border-b  border-gray-200 dark:border-gray-700">
                    <h5 class="font-bold text-gray-900 dark:text-white ">
                        <i class="fas fa-user-edit text-primary-500"></i>
                        Profil Bilgileri
                    </h5>
                </div>
                <div class="card-body">
                    <form class="theme-form" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <!-- Profile Photo -->
                            <div class="col-md-12 text-center mb-4">
                                <div class="relative inline-block">
                                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-200 dark:border-gray-700 mx-auto mb-3">
                                        <img id="preview" src="{{ auth()->user()->profile_photo_url }}" alt="Profil Fotoğrafı" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex justify-center">
                                        <label for="profile_photo" class="cursor-pointer bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm">
                                            <i class="fas fa-camera mr-2"></i> Fotoğraf Değiştir
                                        </label>
                                        <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Önerilen boyut: 512x512px (Max 1MB)</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input label="Ad" name="name" :value="$user->name" :error="$errors->first('name')" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-input label="Soyad" name="last_name" :value="$user->last_name" :error="$errors->first('last_name')" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input label="E-posta Adresi" name="email" type="email" :value="$user->email" :error="$errors->first('email')" required />
                        </div>

                        <hr class="my-4 border-gray-200 dark:border-gray-700">
                        <h6 class="mb-3 font-semibold text-gray-900 dark:text-white">Şifre Değiştir (İsteğe Bağlı)</h6>

                        <div class="mb-3">
                            <x-input label="Mevcut Şifre" name="current_password" type="password" placeholder="Sadece şifre değiştirecekseniz girin" :error="$errors->first('current_password')" />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <x-input label="Yeni Şifre" name="password" type="password" :error="$errors->first('password')" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <x-input label="Yeni Şifre Tekrar" name="password_confirmation" type="password" />
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                             <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Vazgeç</a>
                            <button class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors shadow-sm" type="submit">Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
@endsection
