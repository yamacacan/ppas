@extends('layouts.master')
@section('title', 'Yeni Kullanıcı Ekle')

@section('css')
@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Kullanıcı Ekle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sisteme yeni bir kullanıcı tanımlayın</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('kullanicilar.index') }}" class="text-primary-600 hover:text-primary-700">Kullanıcı İşlemleri</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Kullanıcı Ekle</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-user-plus text-primary-500"></i>
            Yeni Kullanıcı Ekle
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('kullanicilar.store') }}" method="POST">
            @csrf
            
            @php
                $unitOptions = $units->mapWithKeys(function ($unit) {
                    $name = $unit->parent ? $unit->parent->name . ' / ' . $unit->name : $unit->name;
                    return [$unit->id => $name];
                });

                $titleOptions = $titles->pluck('name', 'id');
                
                $roleOptions = $roles->filter(fn($r) => $r->name != 'Super Admin')->pluck('name', 'id');
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Unit -->
                <x-select 
                    label="Birimi" 
                    name="unit_id" 
                    id="unit_id"
                    :options="$unitOptions" 
                    :value="old('unit_id')"
                    placeholder="Birim Seçiniz"
                />

                <!-- Title -->
                <x-select 
                    label="Ünvan" 
                    name="title_id" 
                    id="title_id"
                    :options="$titleOptions" 
                    :value="old('title_id')"
                    :error="$errors->first('title_id')"
                    placeholder="Ünvan Seçiniz"
                />

                <!-- Role -->
                <div class="md:col-span-2">
                    <x-select 
                        label="Rolü" 
                        name="role_id" 
                        id="role_id"
                        :options="$roleOptions" 
                        :value="old('role_id')"
                        placeholder="Rol Seçiniz"
                    />
                </div>

                <!-- Name -->
                <x-input 
                    label="Kullanıcı Adı" 
                    name="name" 
                    id="name"
                    :value="old('name')" 
                    :error="$errors->first('name')"
                />

                <!-- Last Name -->
                <x-input 
                    label="Kullanıcı Soyadı" 
                    name="last_name" 
                    id="last_name"
                    :value="old('last_name')" 
                    :error="$errors->first('last_name')"
                />

                <!-- Email -->
                <x-input 
                    type="email"
                    label="E-Posta" 
                    name="mail" 
                    id="mail"
                    :value="old('mail')" 
                    :error="$errors->first('mail')"
                />

                <!-- Phone -->
                <x-input 
                    label="Telefon" 
                    name="phone" 
                    id="phone"
                    :value="old('phone')" 
                    :error="$errors->first('phone')"
                />

                <!-- Password -->
                <div class="md:col-span-2">
                    <x-input 
                        type="password"
                        label="Şifre" 
                        name="password" 
                        id="password"
                        :error="$errors->first('password')"
                    />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('kullanicilar.index') }}" class="btn btn-secondary">Vazgeç</a>
                <button type="submit" class="btn btn-primary">Kaydet</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')


    <script src="{{ asset('assets/js/custom.js') }}"></script>


@endsection
