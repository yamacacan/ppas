@extends('layouts.master')

@section('title', 'Keyword Düzenle')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Keyword Düzenle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Keyword ayarlarını ve istisnalarını yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('keywords.index') }}" class="text-primary-600 hover:text-primary-700">Keywords</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Düzenle</span>
    </li>
@endsection

@section('content')
<div x-data="{ 
    formData: { 
        match_type: '{{ old('match_type', $keyword->match_type) }}', 
        priority: {{ old('priority', $keyword->priority) }} 
    },
    overrideType: 'unit'
}">
    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Edit Form -->
        <div class="xl:col-span-2 space-y-6">
            <form action="{{ route('keywords.update', $keyword->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card">
                    <div class="card-header border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-edit text-primary-500"></i>
                                Keyword Bilgileri
                            </h5>
                            <span class="badge badge-primary">{{ $keyword->keyword }}</span>
                        </div>
                    </div>
                    
                    <div class="card-body space-y-6">
                        <!-- Category & Keyword -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Keyword
                                </label>
                                <input type="text" 
                                       name="keyword" 
                                       class="form-input @error('keyword') border-red-500 @enderror" 
                                       value="{{ old('keyword', $keyword->keyword) }}" 
                                       placeholder="Örn: Visual Studio Code">
                                @error('keyword')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Varsayılan Kategori
                                </label>
                                <select name="category_id" class="form-select @error('category_id') border-red-500 @enderror">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $keyword->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->getFullPath() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Match Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Eşleşme Tipi
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="match_type" value="exact" x-model="formData.match_type" class="peer sr-only">
                                    <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 hover:border-green-300">
                                        <div class="w-8 h-8 mx-auto bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-xs mb-2">100</div>
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Tam Eşleşme</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="match_type" value="contains" x-model="formData.match_type" class="peer sr-only">
                                    <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:border-blue-300">
                                        <div class="w-8 h-8 mx-auto bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs mb-2">80</div>
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">İçeriyor</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="match_type" value="starts_with" x-model="formData.match_type" class="peer sr-only">
                                    <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/20 hover:border-yellow-300">
                                        <div class="w-8 h-8 mx-auto bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-xs mb-2">70</div>
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Başlıyor</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="match_type" value="ends_with" x-model="formData.match_type" class="peer sr-only">
                                    <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 hover:border-orange-300">
                                        <div class="w-8 h-8 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-xs mb-2">70</div>
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Bitiyor</p>
                                    </div>
                                </label>

                                <label class="relative cursor-pointer">
                                    <input type="radio" name="match_type" value="regex" x-model="formData.match_type" class="peer sr-only">
                                    <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 hover:border-red-300">
                                        <div class="w-8 h-8 mx-auto bg-red-500 rounded-full flex items-center justify-center text-white font-bold text-xs mb-2">60</div>
                                        <p class="text-xs font-medium text-gray-900 dark:text-white">Regex</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Priority & Settings -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Öncelik (1-10)
                                </label>
                                <input type="number" 
                                       name="priority" 
                                       x-model="formData.priority"
                                       class="form-input" 
                                       min="1" 
                                       max="10">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Yüksek öncelikli keyword'ler önce işlenir
                                </p>
                            </div>

                            <div class="pt-6">
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_case_sensitive" class="form-checkbox h-5 w-5 text-primary-600 rounded" value="1" {{ old('is_case_sensitive', $keyword->is_case_sensitive) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Case Sensitive</span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-primary-600 rounded" value="1" {{ old('is_active', $keyword->is_active) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</span>
                                    </label>
                                </div>

                                <!-- Alert Settings -->
                                <div x-data="{ isAlert: {{ $keyword->is_alert ? 'true' : 'false' }} }" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <label class="flex items-center cursor-pointer mb-3">
                                        <input type="checkbox" 
                                               name="is_alert" 
                                               class="form-checkbox h-5 w-5 text-red-600 rounded" 
                                               value="1" 
                                               x-model="isAlert"
                                               {{ old('is_alert', $keyword->is_alert) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Uyarı Gönder (Alert)
                                        </span>
                                    </label>

                                    <div x-show="isAlert" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="pl-7">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Bildirim Gidecek Birim <span class="text-red-500">*</span>
                                        </label>
                                        <select name="alert_unit_id" class="form-select text-sm w-full" :required="isAlert">
                                            <option value="">Birim seçiniz...</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ old('alert_unit_id', $keyword->alert_unit_id) == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-red-500">
                                            Eşleşme olduğunda bu birimdeki kullanıcılara bildirim gider.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('keywords.index') }}" class="btn btn-secondary">İptal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Güncelle
                            </button>
                        </div>
                    </div>
                </div>
            </form>
             @role('Super Admin|Admin')
            <!-- Contextual Overrides -->
            <div class="card">
                <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-code-branch text-purple-500"></i>
                                Bağlamsal İstisnalar
                            </h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Birim veya kullanıcı bazlı özel kategori atamaları</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Add Override Form -->
                    <form action="{{ route('keywords.overrides.store', $keyword->id) }}" method="POST" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tip</label>
                                <select name="type" x-model="overrideType" class="form-select w-full">
                                    <option value="unit">Birim Bazlı</option>
                                    <option value="user">Kullanıcı Bazlı</option>
                                </select>
                            </div>

                            <div x-show="overrideType === 'unit'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birim</label>
                                <select name="unit_id" class="form-select w-full">
                                    <option value="">Seçiniz...</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="overrideType === 'user'" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kullanıcı</label>
                                <select name="computer_user_id" class="form-select w-full">
                                    <option value="">Seçiniz...</option>
                                    @foreach($computerUsers as $cUser)
                                        <option value="{{ $cUser->id }}">{{ $cUser->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hedef Kategori</label>
                                <select name="category_id" class="form-select w-full" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-success w-full">
                                    <i class="fas fa-plus mr-1"></i> Ekle
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Overrides List -->
                    @if($keyword->overrides->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="table w-full">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tip</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Birim / Kullanıcı</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hedef Kategori</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($keyword->overrides as $override)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($override->unit_id)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                        <i class="fas fa-building mr-1"></i> Birim
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                        <i class="fas fa-user mr-1"></i> Kullanıcı
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                @if($override->unit_id)
                                                    {{ $override->unit->name ?? 'Silinmiş Birim' }}
                                                @else
                                                    <div class="flex flex-col">
                                                        <span class="font-medium">{{ $override->computerUser->username ?? 'Silinmiş' }}</span>
                                                        <span class="text-xs text-gray-500">{{ $override->computerUser->name ?? '' }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="badge badge-outline-primary">
                                                    {{ $override->category->name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('keywords.overrides.destroy', $override->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/30 rounded-lg dashed border-2 border-gray-200 dark:border-gray-700">
                            <i class="fas fa-info-circle text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Henüz tanımlanmış bir istisna bulunmuyor.</p>
                        </div>
                    @endif
                </div>
            </div>

             <!-- Alert Exceptions -->
            <div class="card">
                <div class="card-header border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-bell-slash text-red-500"></i>
                                Alert İstisnaları
                            </h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bu birim veya kullanıcılar için bildirim GÖNDERİLMEZ</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Add Exception Form -->
                    <form action="{{ route('keywords.alert-exceptions.store', $keyword->id) }}" method="POST" class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700 mb-6" x-data="{ exceptionType: 'unit' }">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tip</label>
                                <select name="type" x-model="exceptionType" class="form-select w-full">
                                    <option value="unit">Birim Bazlı</option>
                                    <option value="user">Kullanıcı Bazlı</option>
                                </select>
                            </div>

                            <div x-show="exceptionType === 'unit'">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birim</label>
                                <select name="unit_id" class="form-select w-full">
                                    <option value="">Seçiniz...</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div x-show="exceptionType === 'user'" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kullanıcı</label>
                                <select name="computer_user_id" class="form-select w-full">
                                    <option value="">Seçiniz...</option>
                                    @foreach($computerUsers as $cUser)
                                        <option value="{{ $cUser->id }}">{{ $cUser->username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-danger w-full">
                                    <i class="fas fa-plus mr-1"></i> İstisna Ekle
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Exceptions List -->
                    @if($keyword->alertExceptions->count() > 0)
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="table w-full">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tip</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Birim / Kullanıcı</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($keyword->alertExceptions as $exception)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($exception->unit_id)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                        <i class="fas fa-building mr-1"></i> Birim
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                        <i class="fas fa-user mr-1"></i> Kullanıcı
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                @if($exception->unit_id)
                                                    {{ $exception->unit->name ?? 'Silinmiş Birim' }}
                                                @else
                                                    <div class="flex flex-col">
                                                        <span class="font-medium">{{ $exception->computerUser->username ?? 'Silinmiş' }}</span>
                                                        <span class="text-xs text-gray-500">{{ $exception->computerUser->name ?? '' }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <form action="{{ route('keywords.alert-exceptions.destroy', $exception->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Silmek istediğinize emin misiniz?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/30 rounded-lg dashed border-2 border-gray-200 dark:border-gray-700">
                            <i class="fas fa-check-circle text-green-400 text-3xl mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Şu an bildirim engellenen istisna yok.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endrole
        <!-- Sidebar Info -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Stats -->
            <div class="card">
                <div class="card-body">
                    <h6 class="font-bold text-gray-900 dark:text-white mb-4">İstatistikler</h6>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Toplam Eşleşme</span>
                            <span class="font-bold text-primary-600">{{ number_format($keyword->activities_count ?? 0) }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Oluşturma</span>
                            <span class="text-sm font-medium">{{ $keyword->created_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Son Güncelleme</span>
                            <span class="text-sm font-medium">{{ $keyword->updated_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="card bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
                <div class="card-body">
                    <h6 class="font-bold text-blue-900 dark:text-blue-300 mb-2">
                        <i class="fas fa-question-circle mr-1"></i> İstisna Nedir?
                    </h6>
                    <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                        Normalde bu keyword <strong>{{ $keyword->category->name ?? 'Seçili Kategori' }}</strong> kategorisine gider.
                    </p>
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        Ancak belirli bir <strong>Birim</strong> veya <strong>Kullanıcı</strong> bu keyword'ü ürettiğinde farklı bir kategoriye gitmesini istiyorsanız istisna ekleyebilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
