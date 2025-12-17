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
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Roller</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki kullanıcı rollerini tanımlayın</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Rol İşlemleri</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Rol Ekle</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-user-tag text-primary-500"></i>
            Roller
        </h5>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki kullanıcı rollerini tanımlayın</p>
    </div>

    <div class="card-body">
        <!-- Add Role Form -->
        <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <h6 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle text-primary-500"></i>
                Yeni Rol Ekle
            </h6>
            <form action="{{ route('roller.storeRole') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end">
                @csrf
                <div class="flex-1 w-full">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="name">Rol Adı</label>
                    <input id="name" name="name" type="text" placeholder="Örn: Editör"
                        class="form-input block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary whitespace-nowrap">
                    <i class="fas fa-plus mr-1"></i> Rol Ekle
                </button>
            </form>
        </div>

        @if ($message = session('message'))
            <div class="alert alert-success mb-6 flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                <i class="fas fa-check-circle"></i>
                {{ $message }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table w-full" id="general-datatable">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4 w-16">#</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Rol Adı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4 w-32">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @if ($roles->count())
                        @foreach ($roles as $key => $role)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('roller.edit', $role->id) }}" 
                                           class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all" 
                                           title="Düzenle">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('roller.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Emin misiniz?')">
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
    <script src="{{ asset('assets/js/general-datatable.js') }}"></script>


@endsection
