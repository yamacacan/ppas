@extends('layouts.master')
@section('title', 'Birimler')

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
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Birimler</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Organizasyonel birim yapısını yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Birim İşlemleri</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Birimler</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-sitemap text-primary-500"></i>
                    Birim Listesi
                </h5>
            </div>
            <a href="{{ route('birim.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Birim Ekle
            </a>
        </div>
    </div>

    <div class="card-body">
        @if ($message = session('message'))
            <div class="alert alert-success mb-6 flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                <i class="fas fa-check-circle"></i>
                {{ $message }}
            </div>
        @elseif($message = session('error'))
            <div class="alert alert-danger mb-6 flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 rounded-lg p-4 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                <i class="fas fa-exclamation-circle"></i>
                {{ $message }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table w-full" id="basic-1">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">#</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Bağlı Olduğu Birim</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Birim Adı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @if ($units->count())
                        @foreach ($units as $key => $unit)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $unit->parentUnit ? $unit->parentUnit->name : 'Merkez' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $unit->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($unit->id != 1)
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('birim.edit', $unit->id) }}" 
                                               class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all" 
                                               title="Düzenle">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <form action="{{ route('birim.destroy', $unit->id) }}" method="POST" class="inline" onsubmit="return confirm('Emin misiniz?')">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" 
                                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" 
                                                        title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
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
@endsection

