@extends('layouts.master')
@section('title', 'Ünvanlar')

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
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Ünvanlar</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Personel ünvanlarını yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Ünvan İşlemleri</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Ünvanlar</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-id-badge text-primary-500"></i>
                    Ünvan Listesi
                </h5>
            </div>
            <a href="{{ route('unvan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Ünvan Ekle
            </a>
        </div>
    </div>

    <div class="card-body">
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
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4">Ünvan Adı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400 px-6 py-4 w-32">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @if ($titles->count())
                        @foreach ($titles as $key => $title)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $title->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('unvan.edit', $title->id) }}" 
                                           class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all" 
                                           title="Düzenle">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('unvan.destroy', $title->id) }}" method="POST" class="inline" onsubmit="return confirm('Emin misiniz?')">
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
@section('css')
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

@endsection

@section('breadcrumb-title')
<h3>Ünvanlar</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Ünvan İşlemleri</li>
<li class="breadcrumb-item active">Ünvanlar</li>
@endsection

@section('content')


<div class="container-fluid">
    <div class="row">
        <!-- Zero Configuration  Starts-->
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Tüm Ünvanlar</h3>
                    <a href="{{route('unvan.create')}}" class="btn btn-primary" data-bs-original-title="" title=""><i class="icon-plus"></i> Ünvan Ekle</a>
                </div>

                
                
                <div class="card-body">
                    @if ($message = session('message'))
                    <div class="alert alert-success">{{$message}}</div>
                    @endif
                    
                    
                    <div class="table-responsive">
                        <table class="display" id="general-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ünvan</th>
                                    <th></th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @if ($titles->count())

                                    @foreach ($titles as $key=>$title)

                                   

                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$title->name}}</td>
                                            <td>
                                                <ul class="action">
                                                    <li class="edit"> 
                                                        <a href="{{route('unvan.edit',$title->id)}}" class="btn btn-sm"><i class="icon-pencil-alt"></i></a>
                                                        {{-- <button class="btn btn-sm"><i class="icon-icon-pencil-alt"></i></button> --}}
                                                    </li>
                                                    <li class="delete">
                                                        <form id="form-delete" action="{{route('unvan.destroy',$title->id)}}" method="POST" style="display: inline" onsubmit="return confirm('Emin misiniz?')">  
                                                            @csrf
                                                            @method('delete')
                                                            <button class="btn btn-sm"><i class="icon-trash"></i></button>
                                                        </form>
                                                    </li>
                                                    
                                                </ul>
                                            </td>
                                        </tr>
                                        
                                    @endforeach

                                    
                                    
                                @endif
                               
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Zero Configuration  Ends-->
     
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
<script src="{{ asset('assets/js/general-datatable.js') }}"></script>


@endsection