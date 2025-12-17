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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
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
            <div class="overflow-x-auto">
                <table id="computers-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Hostname</th>
                            <th class="px-4 py-3">Son Kullanıcı</th>
                            <th class="px-4 py-3">Domain</th>
                            <th class="px-4 py-3">OS</th>
                            <th class="px-4 py-3">Model</th>
                            <th class="px-4 py-3">RAM (GB)</th>
                            <th class="px-4 py-3">Disk (GB)</th>
                            <th class="px-4 py-3">Son Görülme</th>
                            <th class="px-4 py-3 rounded-tr-lg text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($computers as $computer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $computer->hostname }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $computer->username }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $computer->domain }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $computer->os_version ?? '-' }}
                                </td>
                                <td class="px-4 py-3 truncate max-w-xs" title="{{ $computer->model }}">
                                    {{ $computer->model ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $computer->ram_total_gb ?? '-' }} GB
                                </td>
                                <td class="px-4 py-3">
                                    {{ $computer->disk_total_gb ?? '-' }} GB
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($computer->collected_at)->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('computers.show', $computer->motherboard_uuid) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 text-primary-600 bg-primary-100 hover:bg-primary-200 dark:text-primary-400 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 rounded-lg transition-colors"
                                       title="Detay Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#computers-table')) {
                $('#computers-table').DataTable().destroy();
            }

            $('#computers-table').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
                },
                order: [[7, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [8] }
                ],
                // Simplified DOM to check if duplication persists
                dom: 'lfrtip' 
            });
        });
    </script>
@endsection
