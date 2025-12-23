<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Perfas - Performance Agent System">
    
    <title>@yield('title', 'Perfas') - Performance Agent</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{asset('assets/images/favicon.png')}}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Vite CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('css')
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @yield('style')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased">
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center transition-opacity duration-500" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 800)">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-primary-500"></div>
            <p class="mt-4 text-gray-600 dark:text-gray-400 font-medium">Yükleniyor...</p>
        </div>
    </div>

    <div class="flex h-full" x-data="{ sidebarOpen: window.innerWidth >= 1024, mobileMenuOpen: false }" @resize.window="if(window.innerWidth >= 1024) sidebarOpen = true; else sidebarOpen = false">
        
        <!-- Sidebar -->
        @include('layouts.sidebar')
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header -->
            @include('layouts.header')
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto scrollbar-thin bg-gray-50 dark:bg-gray-900">
                
                <!-- Breadcrumb -->
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            @yield('breadcrumb-title')
                        </div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <a href="{{ route('/') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <i class="fas fa-home"></i>
                                    </a>
                                </li>
                                @yield('breadcrumb-items')
                            </ol>
                        </nav>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="p-6">
                    @yield('content')
                </div>
                
            </main>
            
            <!-- Footer -->
            @include('layouts.footer')
            
        </div>
    </div>

    <!-- Scripts -->
    <!-- Scripts -->
    @include('layouts.script')
</body>
</html>