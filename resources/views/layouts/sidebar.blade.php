<!-- Sidebar -->
<aside 
    x-show="sidebarOpen || mobileMenuOpen" 
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-lg lg:shadow-none"
    :class="{ '-translate-x-full': !sidebarOpen && !mobileMenuOpen }"
    x-cloak
>
    <div class="flex flex-col h-full">
        
        <!-- Logo Section -->
        <div class="flex items-center justify-center px-6 py-8 h-40 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 relative">
            <a href="{{ route('/') }}" class="flex items-center justify-center transition-transform hover:scale-105">
                <img src="{{ asset('assets/images/perfas-light.png') }}" class="w-40 h-40 object-contain dark:hidden" alt="Perfas">
                <img src="{{ asset('assets/images/perfas-dark.svg') }}" class="w-40 h-40 object-contain hidden dark:block" alt="Perfas">
            </a>
            
            <!-- Mobile Close Button -->
            <button 
                @click="mobileMenuOpen = false" 
                class="lg:hidden absolute right-4 top-4 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
            >
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto scrollbar-thin px-3 py-4 space-y-1">
            
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fas fa-home w-5 {{ request()->routeIs('dashboard') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                <span>Dashboard</span>
            </a>

            <!-- Section Divider -->
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Yönetim</p>
            </div>

            <!-- Yönetimsel İşlemler -->
            @if(auth()->user()->can('Birim Yönetimi') || auth()->user()->can('Ünvan Yönetimi') || auth()->user()->hasRole('Super Admin|Admin'))
            <div x-data="{ open: {{ request()->routeIs('birim.*', 'firm-settings.*', 'unvan.*', 'roller.*', 'kullanicilar.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('birim.*', 'firm-settings.*', 'unvan.*', 'roller.*', 'kullanicilar.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-cog w-5 {{ request()->routeIs('birim.*', 'firm-settings.*', 'unvan.*', 'roller.*', 'kullanicilar.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span>Yönetimsel İşlemler</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                    @can('Birim Yönetimi')
                        <a href="{{ route('birim.index') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('birim.index') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Birimler
                        </a>
                        <a href="{{ route('birim.create') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('birim.create') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Birim Ekle
                        </a>
                    @endcan

                    @role('Super Admin|Admin')
                        <a href="{{ route('firm-settings.edit') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('firm-settings.*') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Firma Ayarları
                        </a>
                    @endrole
                    
                    @can('Ünvan Yönetimi')
                        <a href="{{ route('unvan.index') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('unvan.index') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Ünvanlar
                        </a>
                        <a href="{{ route('unvan.create') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('unvan.create') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Ünvan Ekle
                        </a>
                    @endcan
                    
                    @role('Super Admin|Admin')
                        <a href="{{ route('roller.index') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('roller.index') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Roller
                        </a>
                        <a href="{{ route('roller.create') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('roller.create') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Rol İzinleri
                        </a>
                        <a href="{{ route('kullanicilar.index') }}" 
                           class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('kullanicilar.*') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            Kullanıcılar
                        </a>
                    @endrole
                </div>
            </div>
            @endif

            <!-- Section Divider -->
            <div class="pt-4 pb-2">
                <p class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Performans</p>
            </div>

            <!-- Kategori Yönetimi -->
            @can('Kategori Yönetimi')
            <div x-data="{ open: {{ request()->routeIs('categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('categories.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-folder w-5 {{ request()->routeIs('categories.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span>Kategori Yönetimi</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('categories.index') }}" 
                       class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('categories.index') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        Kategoriler
                    </a>
                    <a href="{{ route('categories.create') }}" 
                       class="block px-3 py-2 text-sm transition-all rounded-lg {{ request()->routeIs('categories.create') ? 'text-primary-700 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        Yeni Kategori
                    </a>
                </div>
            </div>
            @endcan

            <!-- Keyword Yönetimi -->
            @can('Keyword Yönetimi')
            <div x-data="{ open: {{ request()->routeIs('keywords.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('keywords.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-key w-5 {{ request()->routeIs('keywords.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span>Keyword Yönetimi</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('keywords.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Keyword'ler
                    </a>
                    <a href="{{ route('keywords.create') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Yeni Keyword
                    </a>
                    <a href="{{ route('keywords.test') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Keyword Test
                    </a>
                    <a href="{{ route('keywords.import') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Toplu İçe Aktar
                    </a>
                </div>
            </div>
            @endcan

            <!-- Aktivite Yönetimi -->
            @can('Aktivite Yönetimi')
            <div x-data="{ open: {{ request()->routeIs('activities.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('activities.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tasks w-5 {{ request()->routeIs('activities.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span>Aktivite Yönetimi</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('activities.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Tüm Aktiviteler
                    </a>
                    <a href="{{ route('activities.tagged') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Taglenmiş
                    </a>
                    <a href="{{ route('activities.untagged') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Taglenmemiş
                    </a>
                    <a href="{{ route('activities.auto-tag') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Otomatik Tagleme
                    </a>
                </div>
            </div>
            @endcan

            <!-- İstatistik & Raporlar -->
            @can('İstatistikler')
            <div x-data="{ open: {{ request()->routeIs('statistics.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('statistics.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-bar w-5 {{ request()->routeIs('statistics.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span>İstatistik & Raporlar</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('statistics.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Kategori İstatistikleri
                    </a>
                    <a href="{{ route('statistics.tagging-rate') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Tagleme Oranı
                    </a>
                    <a href="{{ route('statistics.time-distribution') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Zaman Dağılımı
                    </a>
                    <a href="{{ route('statistics.work-other') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        İş/Diğer Oranı
                    </a>
                    <a href="{{ route('unit-statistics.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Birim İstatistikleri
                    </a>
                </div>
            </div>
            @endcan

            <!-- Kullanıcı Yönetimi -->
            @can('Bilgisayar Kullanıcıları')
            <div x-data="{ open: {{ request()->routeIs('computer-users.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('computer-users.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-users w-5 {{ request()->routeIs('computer-users.*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        <span>Kullanıcı Yönetimi</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('computer-users.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Bilgisayar Kullanıcıları
                    </a>
                    <a href="{{ route('computers.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-all">
                        Bilgisayar Yönetimi
                    </a>
                </div>
            </div>
            @endcan

        </nav>

        <!-- Sidebar Footer -->
        <div class="px-4 h-[60px] flex items-center border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex items-center gap-3 px-2">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">Perfas v1.0</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">© 2025 4Dimension</p>
                </div>
            </div>
        </div>

    </div>
</aside>

<!-- Mobile Overlay -->
<div 
    x-show="mobileMenuOpen" 
    @click="mobileMenuOpen = false" 
    class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 lg:hidden"
    x-cloak
></div>
