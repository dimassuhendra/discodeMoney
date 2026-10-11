<x-app-layout>
    <x-slot name="header">
        Pengaturan & Profil
    </x-slot>

    <div class="space-y-6" x-data="{ resetModalOpen: false }">

        <!-- NOTIFIKASI SUCCESS / ERROR -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- TOP STATS STATUS AKUN, MEMORY MONITOR, & VERSION -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Tanggal Bergabung -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex items-center justify-between">
                <div>
                    <p class="text-xs text-[#8AD6D1] font-medium">Tanggal Bergabung</p>
                    <h3 class="text-xl font-bold text-[#FFF0C5] mt-1">{{ $stats['joined_at'] }}</h3>
                    <p class="text-[10px] text-[#8AD6D1] mt-1">Status: Akun Aktif</p>
                </div>
                <div class="w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center text-lg text-[#FFF0C5]">
                    <i class="fa-solid fa-id-card"></i>
                </div>
            </div>

            <!-- Card 2: Total Record Data -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-medium">Total Record Data</p>
                    <h3 class="text-xl font-bold text-gray-800 mt-1">{{ number_format($stats['grand_total_record']) }} <span class="text-xs font-normal text-gray-400">Transaksi</span></h3>
                    <p class="text-[10px] text-gray-400 mt-1">Pemasukan, Pengeluaran, Investasi & Barang</p>
                </div>
                <div class="w-10 h-10 bg-[#8AD6D1]/20 rounded-2xl flex items-center justify-center text-lg text-[#359FA0]">
                    <i class="fa-solid fa-database"></i>
                </div>
            </div>

            <!-- Card 3: Memory Status (NEW) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-1">
                        <p class="text-xs text-gray-400 font-medium">Penggunaan Memori</p>
                        <i class="fa-solid fa-microchip text-purple-500 text-sm"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $stats['memory_used'] }} <span class="text-xs font-normal text-gray-400">/ {{ $stats['memory_limit'] }}</span></h3>
                    
                    <!-- Progress Bar Memori -->
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $stats['memory_percentage'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 text-right font-medium">{{ $stats['memory_percentage'] }}% Terpakai</p>
                </div>
            </div>

            <!-- Card 4: Versi Sistem -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-medium">Versi Sistem</p>
                    <h3 class="text-lg font-bold text-gray-800 mt-1">{{ $stats['app_version'] }}</h3>
                    <p class="text-[10px] text-emerald-600 font-semibold mt-1"><i class="fa-solid fa-circle-check"></i> Sistem Terkini</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-2xl flex items-center justify-center text-lg text-amber-600">
                    <i class="fa-solid fa-code-branch"></i>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- FORM 1: UPDATE EMAIL / AKUN -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 bg-[#8AD6D1]/20 rounded-xl flex items-center justify-center text-[#359FA0]">
                        <i class="fa-solid fa-user-gear text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-serif font-bold text-lg text-gray-800">Informasi Akun</h3>
                        <p class="text-xs text-gray-400">Kelola alamat email utama Anda</p>
                    </div>
                </div>

                <form action="{{ route('pengaturan.update-profile') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        @error('email')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 text-xs">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Email</span>
                    </button>
                </form>
            </div>

            <!-- FORM 2: UBAH KODE AKSES / PIN -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center space-x-3 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i class="fa-solid fa-key text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-serif font-bold text-lg text-gray-800">Keamanan Kode Akses</h3>
                        <p class="text-xs text-gray-400">Perbarui PIN / Password login Anda</p>
                    </div>
                </div>

                <form action="{{ route('pengaturan.update-kode-akses') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Akses Saat Ini</label>
                        <input type="password" name="kode_akses_lama" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        @error('kode_akses_lama')
                            <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Kode Akses Baru</label>
                            <input type="password" name="kode_akses_baru" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            @error('kode_akses_baru')
                                <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi Kode Akses Baru</label>
                            <input type="password" name="kode_akses_baru_confirmation" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 text-xs">
                        <i class="fa-solid fa-lock"></i>
                        <span>Perbarui Kode Akses</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- SECTION MAINTENANCE: EXPORT BACKUP & DANGER ZONE -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- EXPORT DATA BACKUP -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-3 pb-3 border-b border-gray-100 mb-4">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                            <i class="fa-solid fa-file-csv text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-lg text-gray-800">Backup & Export Data</h3>
                            <p class="text-xs text-gray-400">Download seluruh riwayat keuangan Anda (CSV)</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Fitur ini meng-unduh seluruh data Pemasukan, Pengeluaran, Portofolio Investasi, dan Inventaris Barang Anda ke dalam satu berkas CSV untuk pencadangan mandiri.
                    </p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('pengaturan.export-data') }}" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 text-xs">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Backup Data (.CSV)</span>
                    </a>
                </div>
            </div>

            <!-- DANGER ZONE: RESET DATA -->
            <div class="bg-red-50/50 p-6 rounded-3xl border border-red-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-3 pb-3 border-b border-red-100 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-lg text-red-800">Zone Risiko Tinggi (Danger Zone)</h3>
                            <p class="text-xs text-red-400">Reset seluruh transaksi keuangan</p>
                        </div>
                    </div>
                    <p class="text-xs text-red-600/80 leading-relaxed">
                        Menghapus seluruh histori transaksi Pemasukan, Pengeluaran, Investasi, dan Inventaris Barang secara permanen. Saldo pada seluruh Sumber Dana akan dikembalikan ke Rp0. Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>

                <div class="mt-6">
                    <button @click="resetModalOpen = true" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 text-xs">
                        <i class="fa-solid fa-trash-arrow-up"></i>
                        <span>Reset Seluruh Data Transaksi</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- MODAL KONFIRMASI RESET DATA -->
        <div x-show="resetModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="resetModalOpen = false">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2 text-red-600">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        <h3 class="font-serif font-bold text-lg text-gray-800">Konfirmasi Reset Data</h3>
                    </div>
                    <button @click="resetModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed mb-4">
                    Apakah Anda yakin ingin menghapus seluruh riwayat transaksi? Masukkan <span class="font-bold text-gray-800">Kode Akses saat ini</span> untuk mengonfirmasi.
                </p>

                <form action="{{ route('pengaturan.reset-data') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kode Akses / PIN Anda</label>
                        <input type="password" name="kode_akses_konfirmasi" placeholder="Masukkan Kode Akses" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 text-xs">
                        <i class="fa-solid fa-skull"></i>
                        <span>Ya, Hapus Permanen Seluruh Data</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>