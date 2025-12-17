@extends('layouts.master')

@section('title', 'Kullanıcılar')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Bilgisayar Kullanıcıları</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki kullanıcıları ve birimlerini yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Kullanıcılar</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-users text-primary-500"></i>
                Kullanıcı Listesi
            </h5>
            <div class="text-sm text-gray-500">
                Toplam <span class="font-bold text-gray-900 dark:text-white">{{ $users->count() }}</span> kullanıcı
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table" id="usersTable">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Kullanıcı Adı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Görünen İsim</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Birim</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Sistem Bilgisi</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Toplam Süre</th>
                        <th class="text-right font-semibold text-gray-600 dark:text-gray-400">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                                    <i class="fas fa-user-lock"></i>
                                </div>
                                <code class="text-sm text-pink-600 dark:text-pink-400 font-mono bg-gray-100 dark:bg-gray-800/50 px-2 py-0.5 rounded">
                                    {{ $user->username }}
                                </code>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->name)
                                <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                            @else
                                <span class="text-sm text-gray-400 italic">Tanımsız</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->unit)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    <i class="fas fa-building mr-1"></i> {{ $user->unit->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Global
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-500 font-mono" title="{{ $user->motherboard_uuid }}">
                                    UUID: {{ Str::limit($user->motherboard_uuid, 8) }}
                                </span>
                                <span class="text-[10px] text-gray-400 mt-1">
                                    {{ $user->updated_at->format('d.m.Y H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-gray-900 dark:text-white">
                                {{ number_format(($user->activities_sum_duration_ms ?? 0) / (1000 * 60 * 60), 1) }} Saat
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('computer-users.show', $user->id) }}" 
                                   class="btn btn-sm btn-info text-white" title="İstatistikler">
                                    <i class="fas fa-chart-pie"></i>
                                </a>
                                <a href="{{ route('computer-users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-primary" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "pageLength": 25,
            "language": { "url": "/assets/json/turkish.json" },
            "responsive": true
        });
    });
</script>
@endsection
