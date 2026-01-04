@extends('layouts.master')

@section('title', 'Bilgisayar Yönetimi')

@section('breadcrumb-title')
    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bilgisayar Yönetimi</h3>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
        <a href="{{ route('computers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Bilgisayarlar</a>
    </li>
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
        .dataTables_wrapper .dataTables_length select {
            padding-right: 30px !important;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            @php
                $tableRows = $computers->map(function($computer) {
                    return [
                        'hostname' => '<span class="font-medium text-gray-900 dark:text-white">'.$computer->hostname.'</span>',
                        'username' => '<span class="text-gray-700 dark:text-gray-300">'.$computer->username.'</span>',
                        'domain' => '<span class="text-gray-600 dark:text-gray-400">'.$computer->domain.'</span>',
                        'os' => '<span class="text-gray-600 dark:text-gray-400">'.($computer->os_version ?? '-').'</span>',
                        'model' => '<span class="text-gray-600 dark:text-gray-400 truncate max-w-xs" title="'.$computer->model.'">'.($computer->model ?? '-').'</span>',
                        'ram' => '<span class="text-gray-600 dark:text-gray-400">'.($computer->ram_total_gb ?? '-').' GB</span>',
                        'disk' => '<span class="text-gray-600 dark:text-gray-400">'.($computer->disk_total_gb ?? '-').' GB</span>',
                        'last_seen' => '<span class="text-sm text-gray-500">'.\Carbon\Carbon::parse($computer->collected_at)->format('d.m.Y H:i').'</span>',
                        'actions' => '
                            <div class="text-right">
                                <a href="'.route('computers.show', $computer->motherboard_uuid).'" 
                                   class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all inline-flex items-center justify-center"
                                   title="Detay Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>'
                    ];
                });
            @endphp
            
            <x-data-table 
                :columns="[
                    ['header' => 'Hostname', 'key' => 'hostname'],
                    ['header' => 'Son Kullanıcı', 'key' => 'username'],
                    ['header' => 'Domain', 'key' => 'domain'],
                    ['header' => 'OS', 'key' => 'os'],
                    ['header' => 'Model', 'key' => 'model'],
                    ['header' => 'RAM', 'key' => 'ram'],
                    ['header' => 'Disk', 'key' => 'disk'],
                    ['header' => 'Son Görülme', 'key' => 'last_seen'],
                    ['header' => '', 'key' => 'actions']
                ]" 
                :rows="$tableRows" 
            />
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#computers-table')) {
                $('#computers-table').DataTable().destroy();
            }

            $('#computers-table').DataTable({
                responsive: true,
                language: { url: '/assets/json/turkish.json' },
                order: [[7, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [8] }
                ],
                // Use a simple but standard DOM for this project
                dom: 'lfrtip',
                initComplete: function() {
                    // Slight adjustments to match project style if needed
                }
            });
        });
    </script>
@endsection
