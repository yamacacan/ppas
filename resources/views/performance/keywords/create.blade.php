@extends('layouts.master')

@section('title', 'Yeni Keyword')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Keyword</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Aktivite eşleştirmesi için keyword tanımlayın</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('keywords.index') }}" class="text-primary-600 hover:text-primary-700">Keywords</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Yeni</span>
    </li>
@endsection

@section('content')
@php
    $categoryOptions = ['' => 'Kategori seçiniz...'];
    foreach($categories as $category) {
        $categoryOptions[$category->id] = $category->getFullPath();
    }

    $unitOptions = ['' => 'Birim seçiniz...'];
    foreach($units as $unit) {
        $unitOptions[$unit->id] = $unit->name;
    }
@endphp

<form action="{{ route('keywords.store') }}" method="POST" x-data="{ formData: { category_id: '', keyword: '', match_type: 'contains', priority: 5 } }">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header border-b border-gray-200 dark:border-gray-700">
                    <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-key text-primary-500"></i>
                        Keyword Bilgileri
                    </h5>
                </div>
                <div class="card-body space-y-6">
                    <!-- Category -->
                    <x-select 
                        label="Kategori" 
                        name="category_id" 
                        id="category_id"
                        :options="$categoryOptions"
                        :value="old('category_id')"
                        x-model="formData.category_id"
                        required
                    />

                    <!-- Keyword -->
                    <div>
                        <x-input 
                            label="Keyword" 
                            name="keyword" 
                            id="keyword"
                            x-model="formData.keyword"
                            value="{{ old('keyword') }}"
                            placeholder="Örn: Visual Studio Code"
                            required
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Process name, title veya URL'de aranacak kelime
                        </p>
                    </div>

                    <!-- Match Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Eşleşme Tipi <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="match_type" value="exact" x-model="formData.match_type" class="peer sr-only" {{ old('match_type') == 'exact' ? 'checked' : '' }}>
                                <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 hover:border-green-300">
                                    <div class="w-10 h-10 mx-auto bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm mb-2">100</div>
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">Tam Eşleşme</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="match_type" value="contains" x-model="formData.match_type" class="peer sr-only" {{ old('match_type', 'contains') == 'contains' ? 'checked' : '' }}>
                                <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:border-blue-300">
                                    <div class="w-10 h-10 mx-auto bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mb-2">80</div>
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">İçeriyor</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="match_type" value="starts_with" x-model="formData.match_type" class="peer sr-only" {{ old('match_type') == 'starts_with' ? 'checked' : '' }}>
                                <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-900/20 hover:border-yellow-300">
                                    <div class="w-10 h-10 mx-auto bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-sm mb-2">70</div>
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">Başlıyor</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="match_type" value="ends_with" x-model="formData.match_type" class="peer sr-only" {{ old('match_type') == 'ends_with' ? 'checked' : '' }}>
                                <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/20 hover:border-orange-300">
                                    <div class="w-10 h-10 mx-auto bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm mb-2">70</div>
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">Bitiyor</p>
                                </div>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="match_type" value="regex" x-model="formData.match_type" class="peer sr-only" {{ old('match_type') == 'regex' ? 'checked' : '' }}>
                                <div class="p-3 border-2 rounded-lg text-center transition-all peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 hover:border-red-300">
                                    <div class="w-10 h-10 mx-auto bg-red-500 rounded-full flex items-center justify-center text-white font-bold text-sm mb-2">60</div>
                                    <p class="text-xs font-medium text-gray-900 dark:text-white">Regex</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Priority & Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input 
                                type="number"
                                label="Öncelik (1-10)" 
                                name="priority" 
                                id="priority"
                                x-model="formData.priority"
                                value="{{ old('priority', 5) }}"
                                min="1"
                                max="10"
                                required
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Yüksek öncelik önce eşleşir
                            </p>
                        </div>

                        <div class="space-y-3 pt-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_case_sensitive" class="form-checkbox h-5 w-5 text-primary-600 rounded" value="1" {{ old('is_case_sensitive') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Büyük/küçük harf duyarlı
                                </span>
                            </label>

                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" class="form-checkbox h-5 w-5 text-primary-600 rounded" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Aktif
                                </span>
                            </label>

                            <!-- Alert Settings -->
                            <div x-data="{ isAlert: false }">
                                <label class="flex items-center cursor-pointer mb-3">
                                    <input type="checkbox" 
                                           name="is_alert" 
                                           class="form-checkbox h-5 w-5 text-red-600 rounded" 
                                           value="1" 
                                           x-model="isAlert"
                                           {{ old('is_alert') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Uyarı Gönder (Alert)
                                    </span>
                                </label>

                                <div x-show="isAlert" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="pl-7">
                                    <x-select 
                                        label="Bildirim Gidecek Birim" 
                                        name="alert_unit_id" 
                                        id="alert_unit_id"
                                        :options="$unitOptions"
                                        :value="old('alert_unit_id')"
                                        x-bind:required="isAlert"
                                    />
                                    <p class="mt-1 text-xs text-red-500">
                                        Eşleşme olduğunda bu birimdeki kullanıcılara bildirim gider.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Kaydet
                        </button>
                        <a href="{{ route('keywords.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Live Preview -->
            <div class="card sticky top-6">
                <div class="card-header border-b border-gray-200 dark:border-gray-700">
                    <h6 class="font-bold text-gray-900 dark:text-white text-sm">
                        <i class="fas fa-eye text-primary-500"></i>
                        Önizleme
                    </h6>
                </div>
                <div class="card-body">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <code class="text-sm font-bold text-primary-600 dark:text-primary-400 break-all" x-text="formData.keyword || 'keyword...'"></code>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="badge badge-primary text-xs" x-show="formData.match_type === 'exact'">Tam Eşleşme</span>
                            <span class="badge badge-primary text-xs" x-show="formData.match_type === 'contains'">İçeriyor</span>
                            <span class="badge badge-primary text-xs" x-show="formData.match_type === 'starts_with'">Başlıyor</span>
                            <span class="badge badge-primary text-xs" x-show="formData.match_type === 'ends_with'">Bitiyor</span>
                            <span class="badge badge-primary text-xs" x-show="formData.match_type === 'regex'">Regex</span>
                            <span class="badge badge-secondary text-xs">Öncelik: <span x-text="formData.priority"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Match Type Info -->
            <div class="card bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
                <div class="card-body">
                    <h6 class="font-bold text-blue-900 dark:text-blue-300 mb-3 text-sm">
                        <i class="fas fa-info-circle"></i>
                        Eşleşme Açıklaması
                    </h6>
                    <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                        <p><strong>Tam Eşleşme:</strong> Birebir eşleşme</p>
                        <p><strong>İçeriyor:</strong> Kelime içinde geçer</p>
                        <p><strong>Başlıyor:</strong> Kelime ile başlar</p>
                        <p><strong>Bitiyor:</strong> Kelime ile biter</p>
                        <p><strong>Regex:</strong> Pattern eşleşmesi</p>
                    </div>
                </div>
            </div>

            <!-- Examples -->
            <div class="card bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800">
                <div class="card-body">
                    <h6 class="font-bold text-purple-900 dark:text-purple-300 mb-3 text-sm">
                        <i class="fas fa-lightbulb"></i>
                        Örnekler
                    </h6>
                    <div class="space-y-2 text-sm text-purple-800 dark:text-purple-200">
                        <p><code class="bg-purple-100 dark:bg-purple-900/50 px-2 py-0.5 rounded">chrome</code></p>
                        <p><code class="bg-purple-100 dark:bg-purple-900/50 px-2 py-0.5 rounded">vscode</code></p>
                        <p><code class="bg-purple-100 dark:bg-purple-900/50 px-2 py-0.5 rounded">outlook</code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->

</form>
@endsection
