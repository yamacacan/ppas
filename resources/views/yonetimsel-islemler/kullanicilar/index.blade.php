@extends('layouts.master')
@section('title', 'Kullanıcılar')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kullanıcılar</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki tüm kullanıcıları yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Kullanıcı İşlemleri</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Kullanıcılar</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-users text-primary-500"></i>
                    Kullanıcılar
                </h5>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki tüm kullanıcıları yönetin</p>
            </div>
            <a href="{{route('kullanicilar.create')}}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Kullanıcı Ekle
            </a>
        </div>
    </div>

    <div class="card-body">


        @php
            $isSuperAdmin = auth()->user()->hasRole('Super Admin');
            $isAdmin = auth()->user()->hasRole('Admin');

            $tableRows = $users->filter(function($user) {
                // Super Admin'leri listede gösterme (kendisi değilse)
                return !isset($user->roles[0]) || $user->roles[0]->name != 'Super Admin';
            })->map(function($user) use ($isSuperAdmin, $isAdmin) {
                $targetRole = $user->roles[0]->name ?? null;
                
                // Admin'ler diğer Admin'leri düzenleyemez/silemez
                $canModify = true;
                if ($isAdmin && !$isSuperAdmin && ($targetRole === 'Admin' || $targetRole === 'Super Admin')) {
                    $canModify = false;
                }

                return [
                    'id' => $user->id,
                    'role' => isset($user->roles[0]) 
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">'. $user->roles[0]->name .'</span>' 
                        : 'Rol Yok',
                    'fullname' => '<div class="font-medium text-gray-900 dark:text-white">'.$user->name . ' ' . $user->last_name.'</div>',
                    'email' => '<div class="text-gray-500 dark:text-gray-400">'.$user->email.'</div>',
                    'unit' => isset($user->details->unit) ? $user->details->unit->name : '-',
                    'title' => isset($user->details->title) ? $user->details->title->name : '-',
                    'phone' => isset($user->details) ? $user->details->phone : '-',
                    'can_edit' => $canModify,
                    'can_delete' => $canModify,
                ];
            })->values();
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Rolü', 'key' => 'role'],
                ['header' => 'Adı Soyadı', 'key' => 'fullname'],
                ['header' => 'E-Posta', 'key' => 'email'],
                ['header' => 'Birimi', 'key' => 'unit'],
                ['header' => 'Ünvanı', 'key' => 'title'],
                ['header' => 'İletişim', 'key' => 'phone'],
            ]" 
            :rows="$tableRows" 
            edit-route="kullanicilar.edit" 
            delete-route="kullanicilar.destroy" 
        />
    </div>
</div>
@endsection
