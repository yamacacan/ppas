@extends('layouts.master')

@section('title', 'Kategoriler')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kategoriler</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistemdeki tüm kategorileri yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Performance</span>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Kategoriler</span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-layer-group text-primary-500"></i>
                    Kategori Listesi
                </h5>
            </div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Yeni Kategori
            </a>
        </div>
    </div>
    
    <div class="card-body">


        <div class="overflow-x-auto">
            <table class="table" id="categoriesTable">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Kategori Adı</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Tip</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Parent</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Level</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Keywords</th>
                        <th class="text-left font-semibold text-gray-600 dark:text-gray-400">Durum</th>
                        <th class="text-right font-semibold text-gray-600 dark:text-gray-400">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-sm"
                                     style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                    <i class="fas {{ $category->icon ?? 'fa-folder' }}"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $category->name }}</p>
                                    @if($category->color)
                                        <p class="text-xs font-mono text-gray-400">{{ $category->color }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->type == 'work')
                                <span class="badge badge-primary">
                                    <i class="fas fa-briefcase mr-1"></i> İş
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-coffee mr-1"></i> Diğer
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ $category->parent ? $category->parent->name : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Level {{ $category->level }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge badge-success">
                                {{ $category->keywords->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->is_active)
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    <span class="text-sm text-green-600 font-medium">Aktif</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                    <span class="text-sm text-red-600 font-medium">Pasif</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('categories.edit', $category->id) }}" 
                                   class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all"
                                   title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category->id) }}" 
                                      method="POST" 
                                      class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                            title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                        <!-- Children -->
                        @if(isset($category->children_tree) && $category->children_tree->count() > 0)
                            @foreach($category->children_tree as $child)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 bg-gray-50/50 dark:bg-gray-800/30">
                                <td class="px-6 py-4 pl-12 relative">
                                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300">
                                        <i class="fas fa-level-up-alt fa-rotate-90"></i>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm"
                                             style="background-color: {{ $child->color }}20; color: {{ $child->color }}">
                                            <i class="fas {{ $child->icon ?? 'fa-folder' }} text-xs"></i>
                                        </div>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $child->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($child->type == 'work')
                                        <span class="badge badge-primary text-xs">İş</span>
                                    @else
                                        <span class="badge badge-secondary text-xs">Diğer</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Level {{ $child->level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge badge-success text-xs">
                                        {{ $child->keywords->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($child->is_active)
                                        <span class="text-xs text-green-600 font-medium">Aktif</span>
                                    @else
                                        <span class="text-xs text-red-600 font-medium">Pasif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('categories.edit', $child->id) }}" 
                                           class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-all"
                                           title="Düzenle">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $child->id) }}" 
                                              method="POST" 
                                              class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                                    title="Sil">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#categoriesTable').DataTable({
            "pageLength": 25,
            "ordering": false,
            "language": { "url": "/assets/json/turkish.json" },
            "responsive": true
        });
    });
</script>
@endsection
