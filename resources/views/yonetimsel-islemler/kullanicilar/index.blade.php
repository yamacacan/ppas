@extends('layouts.master')
@section('title', 'Sample Page')

@section('css')
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
        /* DataTables Dark Mode Overrides */
        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_processing,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #d1d5db !important; /* text-gray-300 */
        }
        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #374151; /* bg-gray-700 */
            border-color: #4b5563; /* border-gray-600 */
            color: #f3f4f6; /* text-gray-100 */
        }
        .dark table.dataTable.no-footer {
            border-bottom-color: #374151; /* border-gray-700 */
        }
        .dark table.dataTable tbody tr {
            background-color: transparent !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #d1d5db !important; /* text-gray-300 */
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            color: #ffffff !important;
            background: #4b5563 !important; /* bg-gray-600 */
            border-color: #374151 !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            color: #ffffff !important;
            background: #374151 !important;
            border-color: #4b5563 !important;
        }
    </style>

@endsection

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
        @if ($message = session('message'))
            <div class="alert alert-success mb-4 flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                <i class="fas fa-check-circle"></i>
                {{ $message }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table w-full" id="informationReceivedPersonnels-dt">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">#</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Rolü</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Adı Soyadı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">E-Posta</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Birimi</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Ünvanı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">İletişim</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @if ($users->count())
                        @foreach ($users as $key => $user)
                            @if (isset($user->roles[0]) && $user->roles[0]->name != 'Super Admin')
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4">{{ $key + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ isset($user->roles[0]) ? $user->roles[0]->name : 'Rol Yok' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }} {{ $user->last_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ isset($user->details) ? $user->details->unit->name : '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ isset($user->details) ? $user->details->title->name : '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ isset($user->details) ? $user->details->phone : '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('kullanicilar.edit', $user->id) }}" 
                                               class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all" 
                                               title="Düzenle">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('kullanicilar.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Emin misiniz?')">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" 
                                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" 
                                                        title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/informationReceivedPersonnels-dt.js') }}"></script>


@endsection
