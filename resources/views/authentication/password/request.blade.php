<!DOCTYPE html>
<html class="light" lang="tr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Perfas Şifre Sıfırlama</title>
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
                            <i class="fa-solid fa-key text-white text-3xl"></i>
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
                </div>
            </div>
            <!-- Brand Logo Watermark -->
            <div class="absolute bottom-10 left-10 flex items-center">
                <img src="{{ asset('assets/images/perfas-white.svg') }}" alt="Perfas Enterprise" class="h-28 w-auto opacity-90">
            </div>
        </div>
        <!-- Right Side: Request Form -->
        <div class="w-full lg:w-1/2 flex flex-col bg-white dark:bg-background-dark">
            <!-- Top Nav Style Language Switcher -->
            <header class="flex items-center justify-between px-8 py-6">
                <div class="flex items-center gap-2 lg:hidden">
                    <img src="{{ asset('assets/images/perfas-light.png') }}" alt="Perfas" class="h-10 w-auto dark:hidden">
                    <img src="{{ asset('assets/images/perfas-dark.svg') }}" alt="Perfas" class="h-10 w-auto hidden dark:block">
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
                        <h1 class="text-[#101816] dark:text-white text-4xl font-black leading-tight tracking-[-0.033em] mb-3">Şifremi Unuttum</h1>
                        <p class="text-[#5e8d7c] dark:text-[#a0c4b8] text-base font-normal leading-normal">E-posta adresinizi girin, size şifre sıfırlama bağlantısı gönderelim.</p>
                    </div>

                    @if ($message = session('status'))
                        <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-500/30">
                            <p class="text-green-700 dark:text-green-400 text-sm font-medium">{{ $message }}</p>
                        </div>
                    @endif

                    <!-- Form -->
                    <form class="space-y-6" method="POST" action="{{ route('password.email') }}">
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

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button class="w-full bg-primary-500 hover:bg-[#005235] text-white h-14 rounded-lg font-bold text-lg transition-all shadow-lg shadow-primary-500/20 active:scale-[0.98]" type="submit">
                                Sıfırlama Bağlantısı Gönder
                            </button>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center pt-4">
                            <a href="{{ route('login') }}" class="text-primary-500 dark:text-brand-teal text-sm font-bold hover:underline flex items-center justify-center gap-2">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                                Giriş Sayfasına Dön
                            </a>
                        </div>
                    </form>
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