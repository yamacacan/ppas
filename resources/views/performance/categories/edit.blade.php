@extends('layouts.master')

@section('title', 'Kategori Düzenle')

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori Düzenle</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kategori bilgilerini güncelleyin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-primary-600">Kategoriler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Düzenle</span>
    </li>
@endsection

@section('content')
<div class="card max-w-4xl mx-auto">
    <div class="card-header border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/50 flex items-center justify-center text-orange-600 dark:text-orange-400">
                <i class="fas fa-edit"></i>
            </div>
            Kategori Düzenle: {{ $category->name }}
        </h3>
    </div>

    <form action="{{ route('categories.update', $category->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- İsim ve Tip -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kategori Adı <span class="text-danger">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-tag text-gray-400"></i>
                    </div>
                    <input type="text" name="name" id="name" 
                           class="form-input pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 sm:text-sm shadow-sm transition-colors" 
                           value="{{ old('name', $category->name) }}" required>
                </div>
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tip <span class="text-danger">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-layer-group text-gray-400"></i>
                    </div>
                    <select name="type" id="type" required
                            class="form-select pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 sm:text-sm shadow-sm transition-colors">
                        <option value="">Seçiniz...</option>
                        <option value="work" {{ old('type', $category->type) == 'work' ? 'selected' : '' }}>İş (Work)</option>
                        <option value="other" {{ old('type', $category->type) == 'other' ? 'selected' : '' }}>Diğer (Other)</option>
                    </select>
                </div>
                @error('type')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Parent -->
        <div class="space-y-2">
            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Üst Kategori (Parent)
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-code-branch text-gray-400"></i>
                </div>
                <select name="parent_id" id="parent_id"
                        class="form-select pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 sm:text-sm shadow-sm transition-colors">
                    <option value="">Ana Kategori (Parent yok)</option>
                    @foreach($categories as $cat)
                        @if($cat->id != $category->id && !$cat->isDescendantOf($category))
                            <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->getFullPath() }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kendi altına veya çocuklarının altına taşınamaz.</p>
        </div>

        <!-- Renk ve Icon -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Renk Kodu
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-palette text-gray-400"></i>
                        </div>
                        <input type="text" name="color" id="color" 
                               class="form-input pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 sm:text-sm shadow-sm transition-colors" 
                               value="{{ old('color', $category->color ?? '#7366ff') }}" placeholder="#7366ff">
                    </div>
                    <input type="color" class="h-10 w-12 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer p-0.5 bg-white dark:bg-gray-700" 
                           onchange="document.getElementById('color').value = this.value" value="{{ old('color', $category->color ?? '#7366ff') }}">
                </div>
            </div>

            <div class="space-y-2">
                <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    İkon (Font Awesome)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-icons text-gray-400"></i>
                    </div>
                    <input type="text" name="icon" id="icon" 
                           class="form-input pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 sm:text-sm shadow-sm transition-colors" 
                           value="{{ old('icon', $category->icon) }}" placeholder="fa-folder">
                </div>
            </div>
        </div>

        <!-- Sıralama ve Durum -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div class="space-y-2">
                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Sıralama
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-sort-numeric-down text-gray-400"></i>
                    </div>
                    <input type="number" name="sort_order" id="sort_order" 
                           class="form-input pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 sm:text-sm shadow-sm transition-colors" 
                           value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                </div>
            </div>

            <div class="pt-8">
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 group-hover:text-primary-600 transition-colors">Aktif Durumda</span>
                </label>
            </div>
        </div>

        <!-- Buttons -->
        <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <button type="submit" class="btn btn-primary inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Güncelle</span>
            </button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary inline-flex items-center gap-2">
                <i class="fas fa-times"></i>
                <span>İptal</span>
            </a>
        </div>
    </form>
</div>
@endsection
