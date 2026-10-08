<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - discodeMoney</title>

    <!-- Google Fonts: Outfit & Domine -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Domine:wght@500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            light: '#8AD6D1',
                            teal: '#359FA0',
                            cream: '#FFF0C5',
                            orange: '#FF8C52',
                            dark: '#1e3e3f'
                        }
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Domine', 'serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#8AD6D1] via-[#FFF0C5] to-[#FF8C52] min-h-screen flex items-center justify-center p-4 md:p-6">

    <!-- Card Container Layout Split (Desktop) & Card Overlay (Mobile) -->
    <div class="w-full max-w-4xl bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-white/40">
        
        <!-- SIDE KIRI: Hero / Welcome Panel (Sembunyi di mobile kecil, atau jadi header di mobile) -->
        <div class="md:w-1/2 bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-8 md:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <!-- Decorative SVG Circles -->
            <div class="absolute -top-12 -left-12 w-40 h-40 bg-[#8AD6D1]/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -right-10 w-52 h-52 bg-[#FF8C52]/30 rounded-full blur-2xl"></div>

            <!-- Brand Header -->
            <div class="relative z-10 flex items-center space-x-3">
                <div class="w-10 h-10 bg-[#FFF0C5] rounded-xl flex items-center justify-center text-[#359FA0] font-bold shadow-md">
                    <!-- App Logo SVG -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="font-serif font-bold text-xl tracking-wide text-[#FFF0C5]">discodeMoney</span>
            </div>

            <!-- Hero Illustration / SVG Content -->
            <div class="my-8 md:my-12 flex flex-col items-center text-center relative z-10">
                <div class="w-48 h-48 md:w-56 md:h-56 mb-6 relative">
                    <!-- Finance Vector SVG -->
                    <svg class="w-full h-full drop-shadow-xl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="30" y="40" width="140" height="120" rx="16" fill="#8AD6D1" fill-opacity="0.25"/>
                        <rect x="40" y="55" width="120" height="90" rx="12" fill="#FFF0C5" opacity="0.9"/>
                        <path d="M55 110L80 85L105 100L145 60" stroke="#FF8C52" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="145" cy="60" r="5" fill="#FF8C52"/>
                        <circle cx="100" cy="115" r="14" fill="#359FA0"/>
                        <path d="M100 108V122M96 112C96 110 104 110 104 115C104 120 96 120 96 120" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h1 class="font-serif font-bold text-2xl md:text-3xl text-white mb-2">Selamat Datang Kembali!</h1>
                <p class="text-sm text-[#8AD6D1] max-w-xs font-light">
                    Kelola arus kas, budget harian, dan portofolio investasi Anda dalam satu tempat.
                </p>
            </div>

            <!-- Footer Small Info -->
            <div class="relative z-10 text-xs text-[#8AD6D1]/80 text-center md:text-left">
                &copy; {{ date('Y M') }}
            </div>
        </div>

        <!-- SIDE KANAN: Form Input Kode Akses -->
        <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white/90">
            <div class="max-w-sm w-full mx-auto">
                
                <div class="mb-8">
                    <h2 class="font-serif font-bold text-2xl md:text-3xl text-gray-800 mb-2">Akses Sistem</h2>
                    <p class="text-sm text-gray-500">Masukkan kode akses unik Anda untuk masuk ke sistem pencatatan.</p>
                </div>

                <!-- Form Login -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Field Input Kode Akses -->
                    <div>
                        <label for="kode_akses" class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">
                            Kode Akses
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <!-- Key Icon SVG -->
                                <svg class="w-5 h-5 text-[#359FA0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="kode_akses" 
                                id="kode_akses" 
                                placeholder="••••••••"
                                required
                                autofocus
                                class="w-full pl-11 pr-4 py-3 bg-gray-50/80 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#359FA0] focus:border-[#359FA0] outline-none transition-all duration-200 font-mono text-lg text-gray-800 placeholder-gray-400"
                            >
                        </div>

                        <!-- Validation Error Message -->
                        @error('kode_akses')
                            <p class="mt-2 text-xs text-red-500 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Option Remember Me -->
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#359FA0] focus:ring-[#359FA0]">
                            <span>Ingat Sesi Saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-6 bg-[#FF8C52] hover:bg-[#e07740] active:scale-[0.99] text-white font-semibold rounded-xl shadow-lg shadow-[#FF8C52]/30 transition-all duration-200 flex items-center justify-center space-x-2 group"
                    >
                        <span>Masuk ke Dashboard</span>
                        <!-- Arrow Icon SVG -->
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>
</html>