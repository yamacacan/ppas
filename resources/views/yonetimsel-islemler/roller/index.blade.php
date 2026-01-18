@extends('layouts.master')
@section('title', 'Roller')

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
                    <x-input 
                        label="Rol Adı" 
                        name="name" 
                        id="name"
                        placeholder="Örn: Editör"
                        :error="$errors->first('name')"
                    />
                </div>
                <button type="submit" class="btn btn-primary whitespace-nowrap mb-0.5">
                    <i class="fas fa-plus mr-1"></i> Rol Ekle
                </button>
            </form>
        </div>



        @php
            $tableRows = $roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => '<div class="font-medium text-gray-900 dark:text-white">'.$role->name.'</div>',
                ];
            });
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Rol Adı', 'key' => 'name'],
            ]" 
            :rows="$tableRows" 
            edit-route="roller.edit" 
            delete-route="roller.destroy" 
        />
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/general-datatable.js') }}"></script>


@endsection
