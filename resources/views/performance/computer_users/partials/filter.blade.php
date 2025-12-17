<div class="card mb-6">
    <div class="card-body">
        <form action="{{ $route }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <!-- Tarih Aralığı -->
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Başlangıç Tarihi</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" 
                    class="form-input w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200">
            </div>

            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bitiş Tarihi</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" 
                    class="form-input w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200">
            </div>

            <!-- Filtrele Butonu -->
            <div class="w-full md:w-auto">
                <button type="submit" class="btn btn-primary w-full md:w-auto">
                    <i class="fas fa-filter mr-2"></i> Filtrele
                </button>
            </div>
            
            @if(count($filters) > 1) 
            <div class="w-full md:w-auto">
                <a href="{{ $route }}" class="btn btn-secondary w-full md:w-auto">
                    <i class="fas fa-times mr-2"></i> Temizle
                </a>
            </div>
            @endif
        </form>
    </div>
</div>
