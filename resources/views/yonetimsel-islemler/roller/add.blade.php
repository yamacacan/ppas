@extends('layouts.master')
@section('title', 'Rol İzinleri')

@section('css')
@endsection

@section('style')
@endsection

@section('breadcrumb-title')
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Rol İzinleri</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Rollerin sistem üzerindeki yetkilerini yönetin</p>
    </div>
@endsection

@section('breadcrumb-items')
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <a href="{{ route('roller.index') }}" class="text-primary-600 hover:text-primary-700">Rol İşlemleri</a>
    </li>
    <li class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
        <span class="text-gray-600 dark:text-gray-400">Rol İzinleri</span>
    </li>
@endsection

@section('content')
<div class="card" x-data="{ activeTab: 'performans-modulu' }">
    <div class="card-header border-b border-gray-200 dark:border-gray-700">
        <h5 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="fas fa-shield-alt text-primary-500"></i>
            Role İzin Ekle
        </h5>
    </div>

    <div class="card-body">
        @if ($message = session('message'))
            <div class="alert alert-success mb-6 flex items-center gap-2 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                <i class="fas fa-check-circle"></i>
                {{ $message }}
            </div>
        @endif

        <form action="{{ route('roller.store') }}" method="POST">
            @csrf

            <!-- Role Selection -->
            <div class="mb-8 max-w-xl">
                @php
                     $roleOptions = $roles->filter(fn($r) => $r->name != 'Super Admin')->pluck('name', 'id');
                @endphp
                <x-select 
                    label="Rol Seçin" 
                    name="role_id" 
                    id="role_id"
                    :options="$roleOptions" 
                    placeholder="Lütfen bir rol seçiniz."
                />
            </div>

            <!-- Permissions Matrix -->
            <div class="flex flex-col md:flex-row gap-6 border rounded-xl border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
                <!-- Tabs Sidebar -->
                <div class="w-full md:w-64 bg-gray-50 dark:bg-gray-900 border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700">
                    <div class="flex flex-row md:flex-col overflow-x-auto md:overflow-x-visible">
                        @foreach ($permissionGroups as $groupName => $permissions)
                            <button type="button" 
                                @click="activeTab = '{{ Str::slug($groupName) }}'"
                                :class="{ 'bg-white dark:bg-gray-800 text-primary-600 dark:text-primary-400 border-l-4 border-primary-500 shadow-sm': activeTab == '{{ Str::slug($groupName) }}', 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white border-l-4 border-transparent': activeTab != '{{ Str::slug($groupName) }}' }"
                                class="text-left px-4 py-3 text-sm font-medium transition-all whitespace-nowrap focus:outline-none">
                                {{ $groupName }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="flex-1 p-6">
                    @foreach ($permissionGroups as $groupName => $permissions)
                        <div x-show="activeTab == '{{ Str::slug($groupName) }}'" x-cloak class="space-y-4">
                            <!-- Select All Header -->
                            <div class="pb-4 border-b border-gray-100 dark:border-gray-700">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="selectAll{{ Str::slug($groupName) }}" data-select-all="true" data-parent-id="{{ Str::slug($groupName) }}" class="form-checkbox h-5 w-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500 transition duration-150 ease-in-out">
                                    <span class="ml-2 text-sm font-bold text-gray-900 dark:text-white">Tümünü Seç</span>
                                </label>
                            </div>

                            <!-- Permissions Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                @foreach ($permissions as $permissionName)
                                    @php
                                        $permission = $allPermissions->firstWhere('name', $permissionName);
                                    @endphp
                                    @if($permission)
                                        <div class="p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <label class="inline-flex items-center cursor-pointer w-full">
                                                <input type="checkbox" name="{{ str_replace(' ', '_', $permission->name) }}" class="form-checkbox h-4 w-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500 transition duration-150 ease-in-out selection-child-{{ Str::slug($groupName) }}">
                                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    İzinleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Permission Management Script Loaded');

        // --- SELECT ALL FUNCTIONALITY ---
        
        // Handle "Select All" checkbox clicks
        document.querySelectorAll('[data-select-all="true"]').forEach(selectAllCheckbox => {
            selectAllCheckbox.addEventListener('change', function() {
                const parentId = this.dataset.parentId;
                const childCheckboxes = document.querySelectorAll(`.selection-child-${parentId}`);
                
                childCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });

        // Update "Select All" state when individual checkboxes change
        document.querySelectorAll('[class*="selection-child-"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState(this);
            });
        });

        function updateSelectAllState(childCheckbox) {
            const parentClass = Array.from(childCheckbox.classList).find(cls => cls.startsWith('selection-child-'));
            if (parentClass) {
                const parentId = parentClass.replace('selection-child-', '');
                const selectAllCheckbox = document.querySelector(`#selectAll${parentId}`);
                const childCheckboxes = document.querySelectorAll(`.selection-child-${parentId}`);
                
                if (selectAllCheckbox && childCheckboxes.length > 0) {
                    const allChecked = Array.from(childCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(childCheckboxes).some(cb => cb.checked);
                    
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = !allChecked && someChecked;
                }
            }
        }

        // --- ROLE PERMISSION LOADING ---

        // --- ROLE PERMISSION LOADING ---

        // Config for Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#545454',
            customClass: {
                popup: 'colored-toast dark:!bg-gray-800 dark:!text-white',
                title: 'dark:!text-white'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Use event delegation to catch the change event from the hidden input
        document.addEventListener('change', function(e) {
            console.log('Global change event:', e.target.tagName, e.target.id, e.target.className);
            
            if (e.target && e.target.id === 'role_id') {
                const roleId = e.target.value;
                console.log('Role selected (Delegated):', roleId);

                if (roleId) {
                    // Show loading toast
                    // Reset all permission checkboxes
                    document.querySelectorAll('input[type="checkbox"][class*="selection-child-"]').forEach(cb => {
                        cb.checked = false;
                    });
                    
                    // Reset all "Select All" checkboxes
                    document.querySelectorAll('[data-select-all="true"]').forEach(cb => {
                        cb.checked = false;
                        cb.indeterminate = false;
                    });

                    // Fetch permissions
                    const url = `/roles/${roleId}/permissions`;
                    console.log('Fetching:', url);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(permissions => {
                            console.log('Permissions received:', permissions);

                            if (Array.isArray(permissions)) {
                                let count = 0;
                                permissions.forEach(permissionName => {
                                    // Handle space to underscore conversion
                                    let checkbox = document.querySelector(`input[name="${permissionName}"]`);
                                    
                                    if (!checkbox) {
                                        const underscoredName = permissionName.replace(/ /g, '_');
                                        checkbox = document.querySelector(`input[name="${underscoredName}"]`);
                                    }

                                    if (checkbox) {
                                        checkbox.checked = true;
                                        updateSelectAllState(checkbox);
                                        count++;
                                    }
                                });
                                Toast.fire({ icon: 'success', title: `${count} izin yüklendi` });
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching permissions:', error);
                            Toast.fire({ icon: 'error', title: 'İzinler yüklenirken hata oluştu' });
                        });
                }
            }
        });
    });
</script>
@endsection
