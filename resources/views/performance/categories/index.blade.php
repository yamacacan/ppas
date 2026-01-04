@extends('layouts.master')

@section('title', 'Kategoriler')

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
        @php
            $tableRows = collect();
            foreach($categories as $category) {
                // Parent Category
                $statusHtml = $category->is_active 
                    ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"><span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>Aktif</span>' 
                    : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"><span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>Pasif</span>';

                $typeHtml = $category->type == 'work' 
                    ? '<span class="badge badge-primary"><i class="fas fa-briefcase mr-1"></i> İş</span>' 
                    : '<span class="badge badge-secondary"><i class="fas fa-coffee mr-1"></i> Diğer</span>';
                
                $nameHtml = '
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-sm" style="background-color: '.($category->color ?? '#3b82f6').'20; color: '.($category->color ?? '#3b82f6').'">
                            <i class="fas '.($category->icon ?? 'fa-folder').'"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">'.$category->name.'</p>
                            '.($category->color ? '<p class="text-xs font-mono text-gray-400">'.$category->color.'</p>' : '').'
                        </div>
                    </div>';

                $tableRows->push([
                    'id' => $category->id,
                    'name' => $nameHtml,
                    'type' => $typeHtml,
                    'parent' => $category->parent ? $category->parent->name : '-',
                    'level' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Level '.$category->level.'</span>',
                    'keywords' => '<span class="badge badge-success">'.$category->keywords->count().'</span>',
                    'status' => $statusHtml
                ]);

                // Child Categories
                if(isset($category->children_tree)) {
                    foreach($category->children_tree as $child) {
                        $childStatusHtml = $child->is_active 
                            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"><span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>Aktif</span>' 
                            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"><span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>Pasif</span>';

                        $childTypeHtml = $child->type == 'work' 
                            ? '<span class="badge badge-primary text-xs">İş</span>' 
                            : '<span class="badge badge-secondary text-xs">Diğer</span>';

                        $childNameHtml = '
                            <div class="flex items-center gap-3 pl-8 relative">
                                <div class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-300">
                                    <i class="fas fa-level-up-alt fa-rotate-90"></i>
                                </div>
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm" style="background-color: '.($child->color ?? '#3b82f6').'20; color: '.($child->color ?? '#3b82f6').'">
                                    <i class="fas '.($child->icon ?? 'fa-folder').' text-xs"></i>
                                </div>
                                <span class="font-medium text-gray-700 dark:text-gray-300">'.$child->name.'</span>
                            </div>';

                        $tableRows->push([
                            'id' => $child->id,
                            'name' => $childNameHtml,
                            'type' => $childTypeHtml,
                            'parent' => $category->name,
                            'level' => '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Level '.$child->level.'</span>',
                            'keywords' => '<span class="badge badge-success text-xs">'.$child->keywords->count().'</span>',
                            'status' => $childStatusHtml
                        ]);
                    }
                }
            }
        @endphp

        <x-data-table 
            :columns="[
                ['header' => 'Kategori Adı', 'key' => 'name'],
                ['header' => 'Tip', 'key' => 'type'],
                ['header' => 'Parent', 'key' => 'parent'],
                ['header' => 'Level', 'key' => 'level'],
                ['header' => 'Keywords', 'key' => 'keywords'],
                ['header' => 'Durum', 'key' => 'status'],
            ]" 
            :rows="$tableRows" 
            edit-route="categories.edit" 
            delete-route="categories.destroy" 
        />
    </div>
</div>
@endsection
