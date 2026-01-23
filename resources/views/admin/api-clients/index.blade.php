@extends('layouts.master')

@section('title', 'API İstemci Yönetimi')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">API İstemci Yönetimi</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dış cihazlar için güvenli API anahtarlarını yönetin</p>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h5 class="font-bold">API İstemcileri</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClientModal">
                    <i class="fas fa-plus mr-1"></i> Yeni İstemci Ekle
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Adı</th>
                                <th>API Token (Header: X-Api-Token)</th>
                                <th>AES Şifreleme Anahtarı</th>
                                <th>Durum</th>
                                <th>Son Kullanım</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                            <tr>
                                <td class="font-medium text-gray-900 dark:text-white">{{ $client->name }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 p-1 rounded">{{ $client->token }}</code>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 p-1 rounded">{{ $client->aes_key }}</code>
                                    </div>
                                </td>
                                <td>
                                    @if($client->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-xs text-gray-500">
                                        {{ $client->last_used_at ? $client->last_used_at->diffForHumans() : 'Hiç kullanılmadı' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.api-clients.toggle', $client) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $client->is_active ? 'warning' : 'success' }} btn-xs">
                                                {{ $client->is_active ? 'Durdur' : 'Aktif Et' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.api-clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs">Sil</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Henüz bir istemci tanımlanmamış.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.api-clients.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yeni API İstemcisi Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">İstemci Adı (Örn: C++ Client, Agent #1)</label>
                        <input type="text" name="name" class="form-control" required placeholder="İstemci ismini girin...">
                    </div>
                    <div class="alert alert-info">
                        <p class="text-sm mb-0">
                            İstemci oluşturulduğunda sisteme özel benzersiz bir <strong>Token</strong> ve <strong>AES Anahtarı</strong> otomatik üretilecektir.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Oluştur</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
