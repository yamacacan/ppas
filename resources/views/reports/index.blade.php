@extends('layouts.master')

@section('title', 'Raporlarım')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <!-- New Report Request Card -->
        <div class="card mb-4 border-0 shadow-sm rounded-2xl">
            <div class="card-header bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 p-6">
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 m-0">
                    <i class="fas fa-plus-circle text-blue-500"></i>
                    Yeni Rapor Oluştur
                </h5>
            </div>
            <div class="card-body p-6">
                <form action="{{ route('reports.store') }}" method="POST" id="report-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Rapor Başlığı</label>
                            <input type="text" name="title" class="form-control rounded-lg" placeholder="Örn: Şubat 2026 Aktivite Özeti" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Rapor Türü</label>
                            <select name="type" class="form-select rounded-lg" id="report-type-select">
                                <option value="activities">Genel Aktivite Özeti</option>
                                <option value="user_detail">Kullanıcı Detay Analizi</option>
                                <option value="unit_performance">Birim Performans Analizi</option>
                                <option value="app_usage">Uygulama Kullanım Analizi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Format</label>
                            <select name="format" class="form-select rounded-lg">
                                <option value="pdf">PDF (Zengin Tasarım)</option>
                                <option value="excel">Excel (Veri Listesi)</option>
                            </select>
                        </div>

                        <!-- Date Filters -->
                        <div class="col-md-3">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Başlangıç</label>
                            <input type="date" name="start_date" class="form-control rounded-lg" value="{{ now()->subDays(30)->toDateString() }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Bitiş</label>
                            <input type="date" name="end_date" class="form-control rounded-lg" value="{{ now()->toDateString() }}">
                        </div>

                        <!-- User Selection (Visible for user_detail) -->
                        <div class="col-md-3" id="user-select-col" style="display: none;">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Bilgisayar Kullanıcısı</label>
                            <select name="username" class="form-select rounded-lg">
                                <option value="">Seçiniz...</option>
                                @foreach($computerUsers as $user)
                                    <option value="{{ $user->username }}">{{ $user->name }} ({{ $user->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit Selection (Visible for unit_performance) -->
                        <div class="col-md-3" id="unit-select-col" style="display: none;">
                            <label class="form-label text-sm font-semibold text-gray-700 dark:text-gray-300">Birim (Departman)</label>
                            <select name="unit_id" class="form-select rounded-lg">
                                <option value="">Seçiniz...</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 rounded-lg py-2 font-bold transition-all hover:scale-105">
                                <i class="fas fa-magic me-2"></i> Raporu Talep Et
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reports List Card -->
        <div class="card border-0 shadow-sm rounded-2xl overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="reports-table">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-0">Rapor Bilgisi</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-0">Tür/Format</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-0">Durum / İlerleme</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-0">Tarih</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-0 text-end">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($reports as $report)
                            <tr data-report-id="{{ $report->id }}" data-status="{{ $report->status }}">
                                <td class="px-6 py-4">
                                    <div class="fw-bold text-gray-900 dark:text-white">{{ $report->title }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ ucfirst(str_replace('_', ' ', $report->type)) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($report->format === 'pdf')
                                            <i class="fas fa-file-pdf text-red-500"></i>
                                        @else
                                            <i class="fas fa-file-excel text-green-500"></i>
                                        @endif
                                        <span class="text-sm text-gray-600 dark:text-gray-400 uppercase font-bold">{{ $report->format }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4" style="min-width: 200px;">
                                    <div class="status-container">
                                        @if($report->status === 'processing' || $report->status === 'pending')
                                            <div class="progress rounded-pill" style="height: 10px; margin-bottom: 5px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" 
                                                     style="width: {{ $report->progress }}%" 
                                                     aria-valuenow="{{ $report->progress }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 status-label">
                                                {{ $report->status_label }} (%{{ $report->progress }})
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $report->status_badge }}">
                                                {{ $report->status_label }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $report->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-end action-container">
                                    @if($report->status === 'completed')
                                        <a href="{{ route('reports.show', $report->id) }}" class="btn btn-sm btn-outline-success rounded-lg me-2">
                                            <i class="fas fa-download"></i> İndir
                                        </a>
                                    @endif
                                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-lg" onclick="return confirm('Emin misiniz?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="text-gray-400 mb-2"><i class="fas fa-file-invoice fa-3x"></i></div>
                                    <div class="text-gray-500">Henüz hiç rapor oluşturulmamış.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('report-type-select');
        const userCol = document.getElementById('user-select-col');
        const unitCol = document.getElementById('unit-select-col');

        typeSelect.addEventListener('change', function() {
            userCol.style.display = this.value === 'user_detail' ? 'block' : 'none';
            unitCol.style.display = this.value === 'unit_performance' ? 'block' : 'none';
        });

        // Polling for progress
        let pollingInterval = null;

        function checkProgress() {
            const processingReports = document.querySelectorAll('tr[data-status="processing"], tr[data-status="pending"]');
            
            if (processingReports.length === 0) {
                if (pollingInterval) clearInterval(pollingInterval);
                return;
            }

            fetch('{{ route("reports.progress") }}')
                .then(response => response.json())
                .then(data => {
                    data.forEach(report => {
                        const tr = document.querySelector(`tr[data-report-id="${report.id}"]`);
                        if (tr) {
                            tr.setAttribute('data-status', report.status);
                            const container = tr.querySelector('.status-container');
                            const actionContainer = tr.querySelector('.action-container');

                            if (report.status === 'completed') {
                                container.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Tamamlandı</span>`;
                                // Refresh page for now to get download links or dynamically add them
                                if (!actionContainer.querySelector('.btn-outline-success')) {
                                    location.reload(); 
                                }
                            } else if (report.status === 'failed') {
                                container.innerHTML = `<span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Hata Oluştu</span>`;
                            } else {
                                const bar = container.querySelector('.progress-bar');
                                const label = container.querySelector('.status-label');
                                if (bar) {
                                    bar.style.width = report.progress + '%';
                                    bar.setAttribute('aria-valuenow', report.progress);
                                    label.innerText = (report.status === 'processing' ? 'Hazırlanıyor' : 'Bekliyor') + ' ( %' + report.progress + ')';
                                }
                            }
                        }
                    });
                });
        }

        pollingInterval = setInterval(checkProgress, 3000);
    });
</script>
@endsection
