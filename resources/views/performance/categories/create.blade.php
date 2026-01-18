@extends('layouts.master')

@section('title', 'Yeni Kategori')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Yeni Kategori</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kategori oluşturun ve ayarlarını yapılandırın</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('categories.index') }}" class="text-primary-600 hover:text-primary-700">Kategoriler</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Yeni</span>
    </li>
@endsection

@section('content')
<form action="{{ route('categories.store') }}" method="POST" x-data="{ formData: { name: '', type: '', color: '#3b82f6', icon: 'fa-folder' } }">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header border-b border-gray-200 dark:border-gray-700">
                    <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-folder-plus text-primary-500"></i>
                        Kategori Bilgileri
                    </h5>
                </div>
                <div class="card-body space-y-6">
                    <!-- Name & Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input 
                            label="Kategori Adı" 
                            name="name" 
                            id="name"
                            required
                            placeholder="Kategori adını girin"
                            x-model="formData.name"
                            :error="$errors->first('name')"
                        />

                        <x-select 
                            label="Tip" 
                            name="type" 
                            id="type"
                            :options="['work' => 'İş', 'other' => 'Diğer']"
                            required
                            :error="$errors->first('type')"
                        />
                    </div>

                    <!-- Parent Category -->
                    <div>
                        @php
                            $parentOptions = ['' => 'Ana Kategori (Parent yok)'];
                            foreach($categories as $cat) {
                                $parentOptions[$cat->id] = $cat->getFullPath();
                            }
                        @endphp
                        <x-select 
                            label="Parent Kategori" 
                            name="parent_id" 
                            id="parent_id"
                            :options="$parentOptions"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Parent seçilmezse ana kategori olarak oluşturulur
                        </p>
                    </div>

                    <!-- Color & Icon -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Renk
                            </label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       name="color" 
                                       id="colorPicker" 
                                       x-model="formData.color"
                                       class="form-input flex-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" 
                                       value="{{ old('color', '#3b82f6') }}"
                                       placeholder="#3b82f6">
                                <button type="button" class="btn btn-secondary w-12" id="colorPickerBtn">
                                    <i class="fas fa-palette"></i>
                                </button>
                            </div>
                        </div>

                        <x-input 
                            label="Icon (Font Awesome)" 
                            name="icon" 
                            id="icon"
                            x-model="formData.icon"
                            placeholder="fa-folder"
                            value="{{ old('icon') }}"
                        />
                    </div>

                    <!-- Sort Order & Active -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input 
                                type="number"
                                label="Sıralama" 
                                name="sort_order" 
                                id="sort_order"
                                value="{{ old('sort_order', 0) }}"
                                min="0"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Küçük değerler önce gösterilir
                            </p>
                        </div>

                        <div class="flex items-center pt-8">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="form-checkbox h-5 w-5 text-primary-600 rounded" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Aktif
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Kaydet
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            İptal
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Sidebar -->
        <div class="lg:col-span-1">
            <div class="card sticky top-6">
                <div class="card-header border-b border-gray-200 dark:border-gray-700">
                    <h5 class="font-bold text-gray-900 dark:text-white text-sm">
                        <i class="fas fa-eye text-primary-500"></i>
                        Önizleme
                    </h5>
                </div>
                <div class="card-body">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                             :style="'background-color: ' + formData.color">
                            <i :class="'fas ' + formData.icon + ' text-white text-xl'"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white" x-text="formData.name || 'Kategori Adı'"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-show="formData.type === 'work'">İş</span>
                                <span x-show="formData.type === 'other'">Diğer</span>
                                <span x-show="!formData.type">Tip seçilmedi</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-6 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800">
                <div class="card-body">
                    <h6 class="font-bold text-blue-900 dark:text-blue-300 mb-3">
                        <i class="fas fa-info-circle"></i> Yardım
                    </h6>
                    <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                        <p><strong>Kategori Adı:</strong> Benzersiz bir ad girin</p>
                        <p><strong>Tip:</strong> İş aktiviteleri için "İş"</p>
                        <p><strong>Parent:</strong> Alt kategori için parent seçin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</form>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr"></script>
<script>
    const pickr = Pickr.create({
        el: '#colorPickerBtn',
        theme: 'classic',
        default: '{{ old("color", "#3b82f6") }}',
        swatches: [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
            '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'
        ],
        components: {
            preview: true,
            hue: true,
            interaction: { hex: true, input: true, save: true }
        }
    });

    pickr.on('save', (color) => {
        document.getElementById('colorPicker').value = color.toHEXA().toString();
        document.getElementById('colorPicker').dispatchEvent(new Event('input'));
        pickr.hide();
    });
</script>
@endsection
