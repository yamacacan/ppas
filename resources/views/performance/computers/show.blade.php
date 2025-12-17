@extends('layouts.master')

@section('title', 'Bilgisayar Detayı')

@section('breadcrumb-title')
    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bilgisayar Detayı</h3>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
        <a href="{{ route('computers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Bilgisayarlar</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
        <span class="text-gray-500 dark:text-gray-400">{{ $computer->hostname }}</span>
    </li>
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Hardware Info Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Donanım Bilgileri</h3>
                </div>
                <div class="p-6 space-y-4">
                    
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Hostname</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->hostname }}</span>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Son Kullanıcı</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->username }}</span>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Domain</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->domain }}</span>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">İşletim Sistemi</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->os_version }}</span>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Model</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[200px]" title="{{ $computer->model }}">{{ $computer->model }}</span>
                    </div>

                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Seri Numarası</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->serial_number }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Anakart UUID</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white text-xs">{{ $computer->motherboard_uuid }}</span>
                    </div>

                    <!-- Disk Usage -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Disk Kullanımı</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->disk_usage_percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $computer->disk_usage_percent }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1 text-xs text-gray-400">
                            <span>Kullanılan: {{ $computer->disk_used_gb }} GB</span>
                            <span>Toplam: {{ $computer->disk_total_gb }} GB</span>
                        </div>
                    </div>

                    <!-- RAM Usage -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-500 dark:text-gray-400">RAM Kullanımı</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $computer->ram_usage_percent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $computer->ram_usage_percent }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1 text-xs text-gray-400">
                            <span>Kullanılan: {{ $computer->ram_used_gb }} GB</span>
                            <span>Toplam: {{ $computer->ram_total_gb }} GB</span>
                        </div>
                    </div>

                    <div class="pt-4 text-xs text-right text-gray-400">
                        Son Güncelleme: {{ \Carbon\Carbon::parse($computer->collected_at)->format('d.m.Y H:i:s') }}
                    </div>

                </div>
            </div>
        </div>

        <!-- Installed Apps Info -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-full">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Yüklü Uygulamalar</h3>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ count($apps) }} Uygulama</span>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table id="apps-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 rounded-tl-lg">Uygulama Adı</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($apps as $app)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            {{ $app->app_name }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
             if ($.fn.DataTable.isDataTable('#apps-table')) {
                $('#apps-table').DataTable().destroy();
            }

            $('#apps-table').DataTable({
                responsive: true,
              
                pageLength: 10,
                dom: 'lfrtip'
            });
        });
    </script>
@endsection
