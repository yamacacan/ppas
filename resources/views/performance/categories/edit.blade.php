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
            <x-input 
                label="Kategori Adı" 
                name="name" 
                id="name"
                :value="old('name', $category->name)"
                required
                :error="$errors->first('name')"
                icon="fas fa-tag"
            />

            <x-select 
                label="Tip" 
                name="type" 
                id="type"
                :options="['work' => 'İş', 'other' => 'Diğer']"
                :value="old('type', $category->type)"
                required
                :error="$errors->first('type')"
            />
        </div>

        <!-- Parent -->
        <div class="space-y-2">
            @php
                $parentOptions = ['' => 'Ana Kategori (Parent yok)'];
                foreach($categories as $cat) {
                    if($cat->id != $category->id && !$cat->isDescendantOf($category)) {
                        $parentOptions[$cat->id] = $cat->getFullPath();
                    }
                }
            @endphp
            <x-select 
                label="Üst Kategori (Parent)" 
                name="parent_id" 
                id="parent_id"
                :options="$parentOptions"
                :value="old('parent_id', $category->parent_id)"
            />
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

            <x-input 
                label="İkon (Font Awesome)" 
                name="icon" 
                id="icon"
                :value="old('icon', $category->icon)"
                placeholder="fa-folder"
                icon="fas fa-icons"
            />
        </div>

        <!-- Sıralama ve Durum -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <x-input 
                type="number"
                label="Sıralama" 
                name="sort_order" 
                id="sort_order"
                :value="old('sort_order', $category->sort_order ?? 0)"
                icon="fas fa-sort-numeric-down"
            />

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
