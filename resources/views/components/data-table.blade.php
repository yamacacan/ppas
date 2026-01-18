@props([
    'columns',
    'rows' => [],
    'searchable' => true,
    'loading' => false,
    'editRoute' => null,
    'deleteRoute' => null,
    'routeParams' => ['id'], // Parameters to grab from row data for routes
])

<div x-data="dataTable({
    data: {{ json_encode($rows) }},
    columns: {{ json_encode($columns) }},
    searchable: {{ json_encode($searchable) }},
    rowsPerPage: 10
})" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">

    <!-- Toolbar -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col sm:flex-row gap-4 justify-between items-center">
        
        <!-- Search -->
        @if($searchable)
            <div class="relative w-full sm:w-72">
                <input 
                    type="text" 
                    x-model="searchTerm" 
                    placeholder="Ara..." 
                    class="w-full pl-4 pr-10 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all"
                >
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-sm"></i>
            </div>
        @else
            <div></div> <!-- Spacer -->
        @endif

        <!-- Actions -->
        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <!-- Column Toggle -->
            <div class="relative" x-data="{ open: false }">
                <button 
                    @click="open = !open" 
                    class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 focus:ring-gray-500 px-3 py-1.5 text-sm inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
                >
                    <i class="fas fa-columns"></i>
                    <span>Sütunlar</span>
                </button>

                <div 
                    x-show="open" 
                    @click.away="open = false" 
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-20 p-2"
                    x-transition
                    style="display: none;"
                >
                    <h4 class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase">Görünür Sütunlar</h4>
                    <template x-for="col in columns" :key="col.key || col.header">
                        <label class="flex items-center px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                            <input 
                                type="checkbox" 
                                class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-primary-600 focus:ring-primary-500 mr-2"
                                :checked="visibleColumns[col.key || col.header]"
                                @change="toggleColumn(col.key || col.header)"
                            >
                            <span class="text-sm text-gray-700 dark:text-gray-300" x-text="col.header"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto flex-1">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10">
                <tr>
                    <template x-for="(col, idx) in columns" :key="idx">
                        <th 
                            x-show="visibleColumns[col.key || col.header]" 
                            class="px-6 py-4 font-semibold whitespace-nowrap"
                            x-text="col.header"
                        ></th>
                    </template>
                    @if($editRoute || $deleteRoute)
                        <th class="px-6 py-4 font-semibold text-right sticky right-0 bg-gray-50 dark:bg-gray-700/50">
                            İşlemler
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @if($loading)
                    <tr>
                        <td :colspan="columns.length + 1" class="py-12 text-center">
                            <div class="flex justify-center flex-col items-center gap-2">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                                <span class="text-gray-500 dark:text-gray-400">Yükleniyor...</span>
                            </div>
                        </td>
                    </tr>
                @else
                    <template x-if="paginatedData.length === 0">
                        <tr>
                            <td :colspan="columns.length + 1" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="far fa-folder-open text-3xl opacity-50"></i>
                                    <span>Kayıt bulunamadı.</span>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <template x-for="(row, rIdx) in paginatedData" :key="row.id || rIdx">
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <template x-for="(col, cIdx) in columns" :key="cIdx">
                                <td 
                                    x-show="visibleColumns[col.key || col.header]" 
                                    class="px-6 py-4 whitespace-nowrap text-gray-700 dark:text-gray-300"
                                >
                                    <!-- Simple logic to handle 'render' if it was a component, here we just output text/html -->
                                   <div x-html="row[col.key]"></div>
                                </td>
                            </template>
                            
                            @if($editRoute || $deleteRoute)
                                <td class="px-6 py-4 text-right sticky right-0 bg-white dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($editRoute)
                                            <template x-if="row.can_edit !== false">
                                                <a 
                                                    :href="'{{ route($editRoute, ['mock_id']) }}'.replace('mock_id', row.id)" 
                                                    class="text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 p-1.5 rounded-lg transition-colors"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </template>
                                        @endif
                                        @if($deleteRoute)
                                            <template x-if="row.can_delete !== false">
                                                <form 
                                                    :action="'{{ route($deleteRoute, ['mock_id']) }}'.replace('mock_id', row.id)" 
                                                    method="POST" 
                                                    class="delete-form inline-block"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button 
                                                        type="submit" 
                                                        class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 p-1.5 rounded-lg transition-colors"
                                                    >
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </template>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    </template>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div x-show="filteredData.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-gray-800">
        <div class="text-sm text-gray-500 dark:text-gray-400">
            <span class="font-medium text-gray-900 dark:text-white" x-text="filteredData.length"></span> kayıttan 
            <span class="font-medium text-gray-900 dark:text-white" x-text="(currentPage - 1) * rowsPerPage + 1"></span>-
            <span class="font-medium text-gray-900 dark:text-white" x-text="Math.min(currentPage * rowsPerPage, filteredData.length)"></span> arası gösteriliyor
        </div>

        <div class="flex items-center gap-2">
            <select 
                x-model.number="rowsPerPage" 
                @change="currentPage = 1"
                class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-1.5 outline-none"
            >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>

            <div class="flex gap-1">
                <button 
                    @click="currentPage = Math.max(currentPage - 1, 1)"
                    :disabled="currentPage === 1"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 px-3 py-1.5 text-sm rounded-lg disabled:opacity-50"
                >
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <template x-for="pageNum in getPageNumbers()" :key="pageNum">
                    <button 
                        @click="currentPage = pageNum"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors"
                        :class="currentPage === pageNum ? 'bg-primary-600 text-white' : 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'"
                        x-text="pageNum"
                    ></button>
                </template>

                <button 
                    @click="currentPage = Math.min(currentPage + 1, totalPages)"
                    :disabled="currentPage === totalPages"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 px-3 py-1.5 text-sm rounded-lg disabled:opacity-50"
                >
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@once
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dataTable', (config) => ({
        data: config.data,
        columns: config.columns,
        searchable: config.searchable,
        searchTerm: '',
        currentPage: 1,
        rowsPerPage: config.rowsPerPage,
        visibleColumns: {},

        init() {
            // Initialize visible columns
            this.columns.forEach(col => {
                this.visibleColumns[col.key || col.header] = true;
            });
        },

        get filteredData() {
            if (!this.searchTerm) return this.data;
            const lowerTerm = this.searchTerm.toLowerCase();
            return this.data.filter(item => {
                return this.columns.some(col => {
                    const val = item[col.key];
                    if (val == null) return false;
                    return String(val).toLowerCase().includes(lowerTerm);
                });
            });
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.rowsPerPage);
        },

        get paginatedData() {
            const start = (this.currentPage - 1) * this.rowsPerPage;
            return this.filteredData.slice(start, start + this.rowsPerPage);
        },

        toggleColumn(key) {
            this.visibleColumns[key] = !this.visibleColumns[key];
        },

        getPageNumbers() {
            const total = this.totalPages;
            const current = this.currentPage;
            const pages = [];
            
            if (total <= 5) {
                for (let i = 1; i <= total; i++) pages.push(i);
            } else {
                if (current <= 3) {
                    for (let i = 1; i <= 5; i++) pages.push(i);
                } else if (current >= total - 2) {
                    for (let i = total - 4; i <= total; i++) pages.push(i);
                } else {
                    for (let i = current - 2; i <= current + 2; i++) pages.push(i);
                }
            }
            return pages;
        }
    }));
});
</script>
@endonce
