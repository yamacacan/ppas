<!-- Activity Detail Modal -->
<div id="activityDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-info text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Aktivite Detayı
                        </h3>
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kullanıcı</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white" id="modal-username">-</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Process</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono" id="modal-process">-</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Başlık</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white" id="modal-title-text">-</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">URL</dt>
                                    <dd class="mt-1 text-sm text-blue-600 dark:text-blue-400 break-all">
                                        <a href="#" target="_blank" id="modal-url" class="hover:underline">-</a>
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Başlangıç Zamanı</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white" id="modal-start-time">-</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Süre</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white" id="modal-duration">-</dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Kategoriler</dt>
                                    <dd class="mt-1" id="modal-categories">
                                        <!-- Categories will be inserted here -->
                                    </dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bilgisayar / Domain</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white" id="modal-device">-</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tarayıcı</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white" id="modal-browser">-</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700" onclick="closeModal()">
                    Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(activity) {
        document.getElementById('modal-username').textContent = activity.username;
        document.getElementById('modal-process').textContent = activity.process_name;
        document.getElementById('modal-title-text').textContent = activity.title;
        
        const urlElement = document.getElementById('modal-url');
        if (activity.url) {
            urlElement.textContent = activity.url;
            urlElement.href = activity.url;
            urlElement.parentElement.classList.remove('hidden');
        } else {
            urlElement.parentElement.classList.add('hidden');
        }
        
        // Format dates if needed, or assume they are passed as strings or formatted in JS
        document.getElementById('modal-start-time').textContent = new Date(activity.start_time_utc).toLocaleString('tr-TR');
        document.getElementById('modal-duration').textContent = activity.duration_formatted; // Ensure this is available in JS object
        
        // Categories
        const categoriesContainer = document.getElementById('modal-categories');
        categoriesContainer.innerHTML = '';
        if (activity.categories && activity.categories.length > 0) {
            activity.categories.forEach(cat => {
                const span = document.createElement('span');
                span.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-1';
                span.textContent = cat.name;
                categoriesContainer.appendChild(span);
            });
        } else {
            const span = document.createElement('span');
            span.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            span.textContent = 'Taglenmemiş';
            categoriesContainer.appendChild(span);
        }

        // Additional info
        document.getElementById('modal-device').textContent = (activity.computer_name || '-') + ' / ' + (activity.domain || '-');
        document.getElementById('modal-browser').textContent = activity.browser || '-';

        document.getElementById('activityDetailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('activityDetailModal').classList.add('hidden');
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal();
        }
    });
</script>
