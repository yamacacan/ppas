@extends('layouts.master')

@section('title', 'Firma Ayarları')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Firma Ayarları</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tüm aktiviteleriniz ve uyarılarınız</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Firma Ayarları</span>
    </li>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Firma Ayarları ve Mesai Saatleri</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('firm-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Firma Bilgileri</h5>
                            
                            <div class="mb-3">
                                <label for="firm_name" class="form-label">Firma Adı</label>
                                <input type="text" class="form-control" id="firm_name" name="firm_name" value="{{ old('firm_name', $settings->firm_name) }}">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $settings->email) }}">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Adres</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $settings->address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label">Firma Logosu</label>
                                <input class="form-control" type="file" id="logo" name="logo">
                                @if($settings->logo_path)
                                    <div class="mt-2">
                                        <p class="text-muted text-sm mb-1">Mevcut Logo:</p>
                                        <img src="{{ Storage::url($settings->logo_path) }}" alt="Firma Logosu" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">Mesai Saatleri</h5>
                            <p class="text-muted small">Bu saatler performans ölçümlerinde "Mesai İçi" ve "Mesai Dışı" hesaplamalarında kullanılacaktır.</p>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="work_start_time" class="form-label">Mesai Başlangıç Saati</label>
                                    <input type="time" class="form-control" id="work_start_time" name="work_start_time" value="{{ old('work_start_time', \Carbon\Carbon::parse($settings->work_start_time)->format('H:i')) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="work_end_time" class="form-label">Mesai Bitiş Saati</label>
                                    <input type="time" class="form-control" id="work_end_time" name="work_end_time" value="{{ old('work_end_time', \Carbon\Carbon::parse($settings->work_end_time)->format('H:i')) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Ayarları Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
