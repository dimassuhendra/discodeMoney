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

    <!-- Font Awesome 6.6.0 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
<body class="h-full bg-gray-50 text-gray-800 flex flex-col md:flex-row antialiased" x-data="{ quickAddOpen: false, quickType: 'pengeluaran', isBarang: false }">

    <!-- ========================================== -->
    <!-- DESKTOP SIDEBAR (Sembunyi di Mobile: hidden md:flex) -->
    <!-- ========================================== -->
    <aside class="hidden md:flex md:w-64 lg:w-72 md:flex-col md:fixed md:inset-y-0 bg-[#359FA0] text-white z-30 shadow-xl">
        <!-- Sidebar Brand Logo -->
        <div class="h-16 flex items-center px-6 bg-[#1e3e3f]/20 border-b border-[#8AD6D1]/20 space-x-3">
            <div class="w-9 h-9 bg-[#FFF0C5] rounded-xl flex items-center justify-center text-[#359FA0] font-bold shadow-sm">
                <i class="fa-solid fa-wallet text-lg"></i>
            </div>
            <span class="font-serif font-bold text-lg text-[#FFF0C5] tracking-wide">DiscodeFinance</span>
        </div>

        <!-- Navigation Menu Items -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-6">
            
            <!-- Group 1: Dashboard & Utama -->
            <div>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#FFF0C5] text-[#359FA0] font-semibold shadow-sm' : 'hover:bg-[#8AD6D1]/20 text-white' }}">
                    <i class="fa-solid fa-[#359FA0] fa-chart-pie w-5 text-center {{ request()->routeIs('dashboard') ? 'text-[#359FA0]' : 'text-[#8AD6D1]' }}"></i>
                    <span class="text-sm">Dashboard Utama</span>
                </a>
            </div>

            <!-- Group 2: Transaksi -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Transaksi</p>
                <div class="space-y-1">
                    <a href="{{ route('pengeluaran.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <i class="fa-solid fa-money-bill-transfer w-5 text-center text-[#8AD6D1]"></i>
                        <span>Pengeluaran Harian</span>
                    </a>
                    <a href="{{ route('pemasukan.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center text-[#8AD6D1]"></i>
                        <span>Pemasukan</span>
                    </a>
                </div>
            </div>

            <!-- Group 4: Aset & Sumber Dana -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Manajemen</p>
                <div class="space-y-1">
                    <a href="{{ route('barang.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <i class="fa-solid fa-boxes-stacked w-5 text-center text-[#8AD6D1]"></i>
                        <span>Inventaris Barang</span>
                    </a>
                    <a href="{{ route('investasi.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                        <i class="fa-solid fa-chart-line w-5 text-center text-[#8AD6D1]"></i>
                        <span>Catatan Investasi</span>
                    </a>
                    <a href="{{ route('sumber-dana.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-[#FFF0C5]">
                        <i class="fa-solid fa-vault w-5 text-center text-[#8AD6D1]"></i>
                        <span>Sumber Dana</span>
                    </a>
                </div>
            </div>

            <!-- Group 5: Sistem -->
            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[#8AD6D1]/80 mb-2">Lainnya</p>
                <a href="{{ route('pengaturan.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                    <i class="fa-solid fa-sliders w-5 text-center text-[#8AD6D1]"></i>
                    <span>Pengaturan & Profil</span>
                </a>
                <a href="{{ route('export.pdf') }}" target="_blank" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-sm transition-colors hover:bg-[#8AD6D1]/20 text-white/90">
                    <i class="fa-solid fa-file-pdf w-5 text-center text-[#FF8C52]"></i>
                    <span>Export PDF</span>
                </a>
            </div>
  
        </div>

        <!-- User Logout Panel Bottom Sidebar -->
        <div class="p-4 border-t border-[#8AD6D1]/20 bg-[#1e3e3f]/30">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-sm text-red-200 hover:bg-red-500/20 hover:text-white transition-colors">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
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
        <div class="md:hidden relative bg-gradient-to-b from-[#359FA0] via-[#359FA0] to-[#8AD6D1] text-white pt-4 px-5 pb-16 rounded-b-[2.5rem] shadow-md">
            
            <!-- Row 1: Brand & Dropdown Menu -->
            <div class="flex items-center justify-between mb-4">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-[#FFF0C5] rounded-lg flex items-center justify-center text-[#359FA0] font-bold shadow-sm">
                        <i class="fa-solid fa-wallet text-sm"></i>
                    </div>
                    <span class="font-serif font-bold text-lg text-[#FFF0C5] tracking-wide">discodeFinance</span>
                </div>

                <!-- Dropdown Menu Manajemen -->
                <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-left">
                    <button @click="open = !open" type="button" class="text-xs bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1.5 rounded-full text-white font-medium transition-all flex items-center space-x-1.5 border border-white/20">
                        <i class="fa-solid fa-sliders text-[11px]"></i>
                        <span>Manajemen</span>
                        <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="open" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl ring-1 ring-black/5 z-50 text-[#1e3e3f] py-1 overflow-hidden"
                        style="display: none;">
                        
                        <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs hover:bg-[#8AD6D1]/20 hover:text-[#359FA0] transition-colors font-medium">
                            <i class="fa-solid fa-user text-gray-400 w-4"></i>
                            Lihat Profil
                        </a>
                        <a href="{{ route('sumber-dana.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs hover:bg-[#8AD6D1]/20 hover:text-[#359FA0] transition-colors font-medium">
                            <i class="fa-solid fa-vault text-gray-400 w-4"></i>
                            Sumber Dana
                        </a>
                        <a href="{{ route('export.pdf') }}" target="_blank" class="flex items-center gap-2 px-4 py-2.5 text-xs hover:bg-[#8AD6D1]/20 text-[#359FA0] hover:text-[#1e3e3f] transition-colors font-semibold">
                            <i class="fa-solid fa-file-pdf text-[#FF8C52] w-4"></i>
                            Export PDF
                        </a>

                        <div class="border-t border-gray-100 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors font-medium">
                                <i class="fa-solid fa-right-from-bracket text-red-400 w-4"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Row 2: User Greeting & Avatar -->
            <div class="flex justify-between items-center my-3 pb-4">
                {{-- <div>
                    <p class="text-xs text-[#FFF0C5]/90 font-light">Selamat datang,</p>
                    <h2 class="text-lg font-bold text-white tracking-wide">Dimas Suhendra</h2>
                </div>
                <!-- Profile Avatar / Icon -->
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-[#FFF0C5] border-2 border-white flex items-center justify-center overflow-hidden shadow-sm">
                        <i class="fa-solid fa-user text-[#359FA0] text-lg"></i>
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-white rounded-full"></span>
                </div> --}}
            </div>

            <!-- Floating Card: Isinya Nama Pemilik & Waktu Dinamis Bahasa Indonesia -->
            <div class="absolute -bottom-12 left-5 right-5 bg-gradient-to-br from-[#FFF0C5] via-white to-[#FFF0C5] text-[#1e3e3f] p-4 rounded-2xl shadow-xl border border-white/60">
                <div class="flex justify-between items-center mb-2.5 pb-2 border-b border-gray-200/60">
                    <div>
                        <span class="block text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Selamat Datang,</span>
                        <span class="font-bold text-sm text-[#1e3e3f] tracking-wide">DIMAS SUHENDRA</span>
                    </div>
                    <div class="w-7 h-7 rounded-lg bg-[#359FA0]/10 text-[#359FA0] flex items-center justify-center">
                        <i class="fa-solid fa-id-card text-xs"></i>
                    </div>
                </div>
                
                <!-- Baris Bawah: Hari, Tanggal & Jam (Bahasa Indonesia) -->
                <div class="flex justify-between items-center text-xs font-medium">
                    <div class="flex items-center gap-1.5 text-gray-600">
                        <span>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 font-bold text-[#1e3e3f]">
                        <span>{{ \Carbon\Carbon::now()->format('H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spacer untuk memberi ruang akibat kartu yang melayang (-bottom-12) -->
        <div class="md:hidden h-14"></div>

        <!-- Main Body Inject Page Slot -->
        <main class="flex-1 w-full mx-auto p-5 md:p-6 pt-2 md:pt-6">
            @if(isset($header))
                <div class="mb-6">
                    <h1 class="font-serif font-bold text-2xl md:text-3xl text-gray-800">{{ $header }}</h1>
                </div>
            @endif

            {{ $slot }}

            <div class="h-20 w-full block md:hidden" aria-hidden="true"></div>
        </main>
    </div>

    <!-- ========================================== -->
    <!-- MOBILE FIXED BOTTOM BAR (Fixed Bottom) -->
    <!-- ========================================== -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-gradient-to-b from-[#359FA0] via-[#359FA0] to-[#1e3e3f] border-t border-[#359FA0]/40 z-40 px-3 py-2 shadow-2xl backdrop-blur-lg">
        <div class="flex items-center justify-around relative">
            
            <!-- Menu 1: Beranda / Dashboard -->
            <a href="{{ route('dashboard') }}" 
            class="flex flex-col items-center py-1 px-3.5 rounded-xl text-[10px] font-semibold transition-all duration-200 relative {{ request()->routeIs('dashboard') ? 'text-[#FFF0C5] bg-[#359FA0]/30 shadow-inner' : 'text-[#8AD6D1]/60 hover:text-[#8AD6D1]' }}">
                @if(request()->routeIs('dashboard'))
                    <span class="absolute -top-1 w-1.5 h-1.5 bg-[#FFF0C5] rounded-full shadow-[0_0_8px_#FFF0C5]"></span>
                @endif
                <i class="fa-solid fa-house text-base mb-0.5"></i>
                <span>Beranda</span>
            </a>

            <!-- Menu 2: Pengeluaran -->
            <a href="{{ route('pengeluaran.index') }}" 
            class="flex flex-col items-center py-1 px-3.5 rounded-xl text-[10px] font-semibold transition-all duration-200 relative {{ request()->routeIs('pengeluaran.*') ? 'text-[#FFF0C5] bg-[#359FA0]/30 shadow-inner' : 'text-[#8AD6D1]/60 hover:text-[#8AD6D1]' }}">
                @if(request()->routeIs('pengeluaran.*'))
                    <span class="absolute -top-1 w-1.5 h-1.5 bg-[#FFF0C5] rounded-full shadow-[0_0_8px_#FFF0C5]"></span>
                @endif
                <i class="fa-solid fa-receipt text-base mb-0.5"></i>
                <span>Pengeluaran</span>
            </a>

            <!-- MENU TENGAH: FLOATING ACTION BUTTON (+) QUICK ADD -->
            <div class="relative -top-5">
                <button 
                    @click="quickAddOpen = true" 
                    type="button" 
                    class="w-14 h-14 bg-gradient-to-tr from-[#FF8C52] to-[#ffa372] hover:from-[#e07740] hover:to-[#FF8C52] active:scale-95 text-white rounded-full flex items-center justify-center shadow-lg shadow-[#FF8C52]/30 transition-all border-4 border-[#1e3e3f] focus:outline-none ring-2 ring-[#FF8C52]/50"
                >
                    <i class="fa-solid fa-plus text-2xl drop-shadow-md"></i>
                </button>
            </div>

            <!-- Menu 3: Investasi -->
            <a href="{{ route('investasi.index') }}" 
            class="flex flex-col items-center py-1 px-3.5 rounded-xl text-[10px] font-semibold transition-all duration-200 relative {{ request()->routeIs('investasi.*') ? 'text-[#FFF0C5] bg-[#359FA0]/30 shadow-inner' : 'text-[#8AD6D1]/60 hover:text-[#8AD6D1]' }}">
                @if(request()->routeIs('investasi.*'))
                    <span class="absolute -top-1 w-1.5 h-1.5 bg-[#FFF0C5] rounded-full shadow-[0_0_8px_#FFF0C5]"></span>
                @endif
                <i class="fa-solid fa-chart-line text-base mb-0.5"></i>
                <span>Investasi</span>
            </a>

            <!-- Menu 4: Aset / Barang -->
            <a href="{{ route('barang.index') }}" 
            class="flex flex-col items-center py-1 px-3.5 rounded-xl text-[10px] font-semibold transition-all duration-200 relative {{ request()->routeIs('barang.*') ? 'text-[#FFF0C5] bg-[#359FA0]/30 shadow-inner' : 'text-[#8AD6D1]/60 hover:text-[#8AD6D1]' }}">
                @if(request()->routeIs('barang.*'))
                    <span class="absolute -top-1 w-1.5 h-1.5 bg-[#FFF0C5] rounded-full shadow-[0_0_8px_#FFF0C5]"></span>
                @endif
                <i class="fa-solid fa-boxes-stacked text-base mb-0.5"></i>
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
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Tab Switcher: Pengeluaran vs Pemasukan -->
            <div class="flex bg-gray-100 p-1 rounded-xl mb-5">
                <button 
                    @click="quickType = 'pengeluaran'" 
                    :class="quickType === 'pengeluaran' ? 'bg-[#359FA0] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center space-x-1.5"
                >
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>Pengeluaran</span>
                </button>
                <button 
                    @click="quickType = 'pemasukan'" 
                    :class="quickType === 'pemasukan' ? 'bg-[#359FA0] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                    class="flex-1 py-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center space-x-1.5"
                >
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Pemasukan</span>
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
                <button type="submit" class="w-full mt-3 py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Transaksi</span>
                </button>
            </form>

        </div>
    </div>

</body>
</html>