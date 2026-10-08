<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'DiscodeFinance' }} - Personal Financial Tracker</title>

    <!-- Google Fonts: Outfit & Domine -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Domine:wght@500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk penanganan interaksi Modal & Dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
        body { font-family: 'Outfit', sans-serif; }
        /* Style scrollbar halus untuk sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #8AD6D1; border-radius: 4px; }
    </style>
</head>
<body class="h-full bg-gray-50 text-gray-800 flex flex-col md:flex-row antialiased" x-data="{ quickAddOpen: false, quickType: 'pengeluaran' }">

    <!-- ========================================== -->
    <!-- DESKTOP SIDEBAR (Sembunyi di Mobile: hidden md:flex) -->
    <!-- ========================================== -->
    <aside class="hidden md:flex md:w-64 lg:w-72 md:flex-col md:fixed md:inset-y-0 bg-[#359FA0] text-white z-30 shadow-xl">
        <!-- Sidebar Brand Logo -->
        <div class="h-16 flex items-center px-6 bg-[#1e3e3f]/20 border-b border-[#8AD6D1]/20 space-x-3">
            <div class="w-9 h-9 bg-[#FFF0C5] rounded-xl flex items-center justify-center text-[#359FA0] font-bold shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="font-serif font-bold text-lg text-[#FFF0C5] tracking-wide">DiscodeFinance</span>
        </div>

        <!-- Navigation Menu Items -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-6">
            
            <!-- Group 1: Dashboard & Utama -->
            <div>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#FFF0C5] text-[#359FA0] font-semibold shadow-sm' : 'hover:bg-[#8AD6D1]/20 text-white' }}">
                    <span>📊</span>
                    <span class="text-sm">Dashboard Utama</span>
                </a>
            </div>

            <!-- Group 2: Transaksi -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Transaksi</p>
                <div class="space-y-1">
                    <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <span>💸</span>
                        <span>Pengeluaran Harian</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <span>💰</span>
                        <span>Pemasukan</span>
                    </a>
                </div>
            </div>

            <!-- Group 3: Portofolio Investasi -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Investasi</p>
                <div class="space-y-1">
                    <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <span>📈</span>
                        <span>Ringkasan & Log</span>
                    </a>
                    <a href="#" class="flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <div class="flex items-center space-x-3">
                            <span>📝</span>
                            <span>Draft Investasi</span>
                        </div>
                        <span class="bg-[#FF8C52] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">2</span>
                    </a>
                </div>
            </div>

            <!-- Group 4: Aset & Sumber Dana -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Manajemen</p>
                <div class="space-y-1">
                    <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <span>📦</span>
                        <span>Inventaris Barang</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <span>👛</span>
                        <span>Sumber Dana</span>
                    </a>
                </div>
            </div>

            <!-- Group 5: Sistem -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Lainnya</p>
                <a href="#" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                    <span>⚙️</span>
                    <span>Pengaturan & Profil</span>
                </a>
            </div>

        </div>

        <!-- User Logout Panel Bottom Sidebar -->
        <div class="p-4 border-t border-[#8AD6D1]/20 bg-[#1e3e3f]/30">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-sm text-red-200 hover:bg-red-500/20 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ========================================== -->
    <!-- MAIN CONTENT AREA -->
    <!-- ========================================== -->
    <div class="flex-1 md:pl-64 lg:pl-72 flex flex-col min-h-screen pb-24 md:pb-8">
        
        <!-- Top Navbar Mobile Only (Sembunyi di Desktop) -->
        <header class="md:hidden bg-[#359FA0] text-white px-5 py-4 flex items-center justify-between sticky top-0 z-20 shadow-md">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-[#FFF0C5] rounded-lg flex items-center justify-center text-[#359FA0] font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="font-serif font-bold text-lg text-[#FFF0C5]">DiscodeFinance</span>
            </div>
            
            <a href="#" class="text-xs bg-[#8AD6D1]/30 hover:bg-[#8AD6D1]/50 px-3 py-1.5 rounded-full text-white font-medium transition-colors">
                ⚙️ Profil
            </a>
        </header>

        <!-- Main Body Inject Page Slot -->
        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
            @if(isset($header))
                <div class="mb-6">
                    <h1 class="font-serif font-bold text-2xl md:text-3xl text-gray-800">{{ $header }}</h1>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- ========================================== -->
    <!-- MOBILE FIXED BOTTOM BAR (Fixed Bottom) -->
    <!-- ========================================== -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-40 px-2 py-2 shadow-lg">
        <div class="flex items-center justify-around relative">
            
            <!-- Menu 1: Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-3 text-xs font-medium {{ request()->routeIs('dashboard') ? 'text-[#359FA0]' : 'text-gray-400 hover:text-gray-600' }}">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Beranda</span>
            </a>

            <!-- Menu 2: Transaksi -->
            <a href="#" class="flex flex-col items-center py-1 px-3 text-xs font-medium text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Transaksi</span>
            </a>

            <!-- MENU TENGAH: FLOATING ACTION BUTTON (+) QUICK ADD -->
            <div class="relative -top-5">
                <button 
                    @click="quickAddOpen = true" 
                    type="button" 
                    class="w-14 h-14 bg-[#FF8C52] hover:bg-[#e07740] active:scale-95 text-white rounded-full flex items-center justify-center shadow-lg shadow-[#FF8C52]/40 transition-all border-4 border-gray-50 focus:outline-none"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
            </div>

            <!-- Menu 3: Investasi -->
            <a href="#" class="flex flex-col items-center py-1 px-3 text-xs font-medium text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span>Investasi</span>
            </a>

            <!-- Menu 4: Barang & Aset -->
            <a href="#" class="flex flex-col items-center py-1 px-3 text-xs font-medium text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Aset</span>
            </a>

        </div>
    </nav>

    <!-- ========================================== -->
    <!-- QUICK ADD TRANSACTION MODAL (Pop-up) -->
    <!-- ========================================== -->
    <div 
        x-show="quickAddOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
        style="display: none;"
    >
        <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="quickAddOpen = false">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-serif font-bold text-xl text-gray-800">Tambah Transaksi Cepat</h3>
                <button @click="quickAddOpen = false" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Tab Switcher: Pengeluaran vs Pemasukan -->
            <div class="flex bg-gray-100 p-1 rounded-xl mb-5">
                <button 
                    @click="quickType = 'pengeluaran'" 
                    :class="quickType === 'pengeluaran' ? 'bg-[#359FA0] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all"
                >
                    💸 Pengeluaran
                </button>
                <button 
                    @click="quickType = 'pemasukan'" 
                    :class="quickType === 'pemasukan' ? 'bg-[#359FA0] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all"
                >
                    💰 Pemasukan
                </button>
            </div>

            <!-- Form Quick Action -->
            <form action="{{ route('quick-transaction.store') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Pass Tipe Transaksi (Pengeluaran / Pemasukan) -->
                <input type="hidden" name="type" :value="quickType">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nominal (Rp)</label>
                    <input type="number" name="jumlah" placeholder="0" required min="1" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Dana</label>
                    <select name="sumber_dana_id" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        @foreach(Auth::user()->sumberDana as $sd)
                            <option value="{{ $sd->id }}">{{ $sd->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Makan Siang / Beli Saham BBCA" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <!-- Toggle Opsi Catat Aset (Khusus Pengeluaran) -->
                <div x-show="quickType === 'pengeluaran'" class="pt-2 border-t border-gray-100">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_barang" value="1" x-model="isBarang" class="w-4 h-4 text-[#359FA0] rounded border-gray-300 focus:ring-[#359FA0]">
                        <span class="text-xs font-medium text-gray-700">Catat sebagai Barang / Aset?</span>
                    </label>

                    <!-- Detail Tambahan Barang jika Dicentang -->
                    <div x-show="isBarang" class="mt-3 space-y-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 mb-1">Jenis Barang</label>
                            <select name="jenis_barang" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs outline-none">
                                <option value="barang_mati">Barang Mati</option>
                                <option value="barang_hidup">Barang Hidup / Aset</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 mb-1">Tempat Beli (Opsional)</label>
                            <input type="text" name="tempat_beli" placeholder="Tokopedia, Offline Store, dll" class="w-full px-2.5 py-1.5 bg-white border border-gray-200 rounded-lg text-xs outline-none">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full mt-3 py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all">
                    Simpan Transaksi
                </button>
            </form>

        </div>
    </div>

</body>
</html>