<!-- Header -->
<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 h-[70px] flex items-center">
    <div class="flex items-center justify-between w-full">
        
        <!-- Left Side: Toggle & Search -->
        <div class="flex items-center gap-4">
            <!-- Sidebar Toggle (Desktop) -->
            <button 
                @click="sidebarOpen = !sidebarOpen" 
                class="hidden lg:block text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <!-- Mobile Menu Toggle -->
            <button 
                @click="mobileMenuOpen = true" 
                class="lg:hidden text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <!-- Search Bar -->
            <div class="hidden md:block relative">
                <input 
                    type="text" 
                    placeholder="Ara..." 
                    class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <!-- Right Side: Actions & Profile -->
        <div class="flex items-center gap-3">
            
            <!-- Dark Mode Toggle -->
            <button 
                @click="toggleDarkMode()" 
                class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all"
                title="Dark Mode"
            >
                <i class="fas fa-moon text-lg dark:hidden"></i>
                <i class="fas fa-sun text-lg hidden dark:inline"></i>
            </button>
            
            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    @click="open = !open" 
                    class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all relative"
                    title="Bildirimler"
                >
                    <i class="fas fa-bell text-lg"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    @endif
                </button>
                
                <!-- Notifications Dropdown -->
                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                    x-cloak
                >
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Bildirimler</h3>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.readAll') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-primary-500 hover:text-primary-700 font-medium">Tümünü Okundu Say</button>
                            </form>
                        @endif
                    </div>
                    <div class="max-h-96 overflow-y-auto scrollbar-thin">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <div class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0 relative group">
                                <div class="flex items-start gap-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-white truncate">
                                            {{ $notification->data['message'] ?? 'Yeni Bildirim' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                        @if(isset($notification->data['action_url']))
                                            <a href="{{ $notification->data['action_url'] }}" class="text-xs text-primary-500 hover:underline mt-1 block">
                                                Görüntüle / İndir
                                            </a>
                                        @elseif(isset($notification->data['url']))
                                            <a href="{{ route('dokuman.download', ['path' => $notification->data['url']]) }}" class="text-xs text-primary-500 hover:underline mt-1 block">
                                                Dosyayı İndir
                                            </a>
                                        @endif
                                    </div>
                                    <!-- Mark as read for individual item -->
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Okundu işaretle">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="far fa-bell-slash text-2xl mb-2 block"></i>
                                <span class="text-sm">Yeni bildirim yok</span>
                            </div>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                        <a href="{{ route('notifications.index') }}" class="text-sm text-primary-500 hover:text-primary-700 font-medium">Tümünü Gör</a>
                    </div>
                </div>
            </div>
            
            <!-- User Profile -->
            <div x-data="{ open: false }" class="relative">
                <button 
                    @click="open = !open" 
                    class="flex items-center gap-3 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all"
                >
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ Auth::user()->name ?? 'Kullan cı' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ Auth::user()->email ?? 'user@example.com' }}
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 dark:border-gray-600">
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile Photo" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>
                
                <!-- Profile Dropdown -->
                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                    x-cloak
                >
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Kullanıcı' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Auth::user()->email ?? 'user@example.com' }}</p>
                    </div>
                    <div class="py-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-user w-4"></i>
                            <span>Profilim</span>
                        </a>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors w-full text-left">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                <span>Çıkış Yap</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</header>
