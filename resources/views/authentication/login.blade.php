<!DOCTYPE html>
<html class="light" lang="tr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Perfas Giriş Ekranı</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .mesh-gradient {
            background-color: #006642;
            background-image: 
                radial-gradient(at 0% 0%, hsla(158, 82%, 25%, 1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(115, 86%, 35%, 1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(164, 100%, 15%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(145, 100%, 20%, 1) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display">
    <div class="flex min-h-screen w-full overflow-hidden">
        <!-- Left Side: Brand Image/Graphic (Hidden on mobile) -->
        <div class="relative hidden lg:flex lg:w-1/2 mesh-gradient items-center justify-center p-12 overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary-500/30 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-brand-teal/20 rounded-full blur-3xl"></div>
            <div class="relative z-10 w-full max-w-lg">
                <!-- Abstract Glassmorphism UI Elements -->
                <div class="glass-card rounded-xl p-8 shadow-2xl mb-6 transform -rotate-2 hover:rotate-0 transition-transform duration-500">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="size-12 rounded-lg bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-chart-pie text-white text-3xl"></i>
                        </div>
                        <div>
                            <div class="h-2 w-24 bg-white/40 rounded-full mb-2"></div>
                            <div class="h-2 w-16 bg-white/20 rounded-full"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="h-4 w-full bg-white/10 rounded-lg"></div>
                        <div class="h-4 w-4/5 bg-white/10 rounded-lg"></div>
                        <div class="h-4 w-3/4 bg-white/10 rounded-lg"></div>
                    </div>
                    <div class="mt-8 flex justify-between items-end">
                        <div class="flex gap-2">
                            <div class="h-12 w-3 bg-white/40 rounded-t-full"></div>
                            <div class="h-16 w-3 bg-white/60 rounded-t-full"></div>
                            <div class="h-10 w-3 bg-white/30 rounded-t-full"></div>
                            <div class="h-20 w-3 bg-white/80 rounded-t-full"></div>
                        </div>
                        <div class="text-white/80 text-sm font-medium">Performans Analizi</div>
                    </div>
                </div>
                <div class="glass-card rounded-xl p-6 shadow-2xl ml-auto w-2/3 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-brand-teal/40 flex items-center justify-center">
                            <i class="fa-solid fa-arrow-trend-up text-white"></i>
                        </div>
                        <div class="flex-1">
                            <div class="h-2 w-full bg-white/30 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Brand Logo Watermark -->
            <div class="absolute bottom-10 left-10 flex items-center">
                <img src="{{ asset('assets/images/perfas-white.svg') }}" alt="Perfas Enterprise" class="h-28 w-auto opacity-90">
            </div>
        </div>
        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col bg-white dark:bg-background-dark">
            <!-- Top Nav Style Language Switcher -->
            <header class="flex items-center justify-between px-8 py-6">
                <div class="flex items-center gap-2 lg:hidden">
                <div class="flex items-center gap-2 lg:hidden">
                    <img src="{{ asset('assets/images/perfas-light.png') }}" alt="Perfas" class="h-10 w-auto dark:hidden">
                    <img src="{{ asset('assets/images/perfas-dark.svg') }}" alt="Perfas" class="h-10 w-auto hidden dark:block">
                </div>
                </div>
                <div class="ml-auto">
                    <button class="flex min-w-[50px] cursor-pointer items-center justify-center rounded-lg h-10 px-4 bg-[#f0f5f3] dark:bg-primary-500/20 text-[#101816] dark:text-white text-sm font-bold">
                        <span>TR</span>
                    </button>
                </div>
            </header>
            <main class="flex-1 flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-12">
                <div class="max-w-[440px] w-full mx-auto">
                    <!-- Page Heading -->
                    <div class="mb-10">
                        <h1 class="text-[#101816] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em] mb-3">Hoş Geldiniz</h1>
                        <p class="text-[#5e8d7c] dark:text-[#a0c4b8] text-base font-normal leading-normal">Perfas performans yönetim paneline erişmek için giriş yapın.</p>
                    </div>
                    <!-- Form -->
                    <form class="space-y-5" method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Email Field -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[#101816] dark:text-white text-sm font-semibold leading-normal" for="email">E-posta</label>
                            <input id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="form-input w-full rounded-lg text-[#101816] focus:outline-0 focus:ring-2 focus:ring-primary-500/20 border border-[#dae7e2] dark:border-primary-500/30 bg-white dark:bg-background-dark/50 focus:border-primary-500 h-14 placeholder:text-[#5e8d7c]/60 p-[15px] text-base font-normal transition-colors @error('email') border-red-500 @enderror" 
                                placeholder="eposta@perfas.com" type="email"/>
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Password Field -->
                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <label class="text-[#101816] dark:text-white text-sm font-semibold leading-normal" for="password">Şifre</label>
                                @if (Route::has('password.request'))
                                    <a class="text-primary-500 dark:text-brand-teal text-sm font-semibold hover:underline" href="{{ route('password.request') }}">Şifremi Unuttum</a>
                                @endif
                            </div>
                            <div class="relative flex items-center" x-data="{ show: false }">
                                <input id="password" name="password" required
                                    :type="show ? 'text' : 'password'"
                                    class="form-input w-full rounded-lg text-[#101816] focus:outline-0 focus:ring-2 focus:ring-primary-500/20 border border-[#dae7e2] dark:border-primary-500/30 bg-white dark:bg-background-dark/50 focus:border-primary-500 h-14 placeholder:text-[#5e8d7c]/60 p-[15px] pr-12 text-base font-normal transition-colors @error('password') border-red-500 @enderror" 
                                    placeholder="••••••••" />
                                <button type="button" @click="show = !show" class="absolute right-4 text-[#5e8d7c] hover:text-primary-500 transition-colors flex items-center justify-center">
                                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                             @error('password')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Remember Me -->
                        <div class="flex items-center gap-3 py-2">
                            <input class="w-5 h-5 rounded border-[#dae7e2] text-primary-500 focus:ring-primary-500" id="remember" name="remember" type="checkbox"/>
                            <label class="text-[#5e8d7c] dark:text-[#a0c4b8] text-sm font-medium cursor-pointer" for="remember">Beni Hatırla</label>
                        </div>
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button class="w-full bg-primary-500 hover:bg-[#005235] text-white h-14 rounded-lg font-bold text-lg transition-all shadow-lg shadow-primary-500/20 active:scale-[0.98]" type="submit">
                                Giriş Yap
                            </button>
                        </div>
                    </form>
                    <!-- Footer Options -->
                    <!-- 
                    <div class="mt-12 text-center">
                        <p class="text-[#5e8d7c] dark:text-[#a0c4b8] text-sm">
                            Hesabınız yok mu? <a class="text-primary-500 dark:text-brand-teal font-bold hover:underline" href="{{ Route::has('register') ? route('register') : '#' }}">Kaydolun</a>
                        </p>
                    </div> 
                    -->
                </div>
            </main>
            <footer class="p-8 text-center lg:text-left">
                <p class="text-[#5e8d7c] dark:text-[#a0c4b8]/50 text-xs">
                    © {{ date('Y') }} Perfas Performance Systems. Tüm hakları saklıdır.
                </p>
            </footer>
        </div>
    </div>
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>