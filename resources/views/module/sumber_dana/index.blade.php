<x-app-layout>
    <x-slot name="header">
        Manajemen Sumber Dana
    </x-slot>

    <div class="space-y-6" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        editData: { id: null, nama: '', budget: '', budget_harian: '' } 
    }">

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

        <!-- TOP CARDS SUMMARY & ACTION BUTTON -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Card 1: Total Seluruh Kas -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex items-center justify-between">
                <div>
                    <p class="text-xs text-[#8AD6D1] font-medium">Total Akumulasi Kas</p>
                    <h3 class="text-2xl md:text-3xl font-bold text-[#FFF0C5] mt-1">Rp{{ number_format($totalKas, 0, ',', '.') }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-xl text-[#FFF0C5]">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>

            <!-- Card 2: Jumlah Sumber Dana Terdaftar -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-400 font-medium">Sumber Dana Terdaftar</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalSumberDana }} Pos</h3>
                </div>
                <div class="w-12 h-12 bg-[#8AD6D1]/20 rounded-2xl flex items-center justify-center text-xl text-[#359FA0]">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>

            <!-- Card 3: Button Tambah Sumber Dana Baru -->
            <div class="bg-white p-5 rounded-2xl border-2 border-dashed border-[#8AD6D1] shadow-sm flex flex-col justify-center items-center text-center">
                <button 
                    @click="createModalOpen = true"
                    class="w-full h-full py-2 px-4 bg-[#FF8C52] hover:bg-[#e07740] active:scale-98 text-white font-semibold text-xs rounded-xl shadow-sm transition-all flex items-center justify-center space-x-2"
                >
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Tambah Sumber Dana Baru</span>
                </button>
            </div>

        </div>

        <!-- GRID CARDS SUMBER DANA -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($sumberDanaList as $sd)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between space-y-4 hover:border-[#8AD6D1] transition-all">
                    
                    <!-- Header Card -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-serif font-bold text-lg text-gray-800 flex items-center space-x-2">
                                <i class="fa-solid fa-wallet text-[#359FA0]"></i>
                                <span>{{ $sd->nama }}</span>
                            </h4>

                            <!-- Menu Aksi (Edit & Delete) -->
                            <div class="flex items-center space-x-2">
                                <button 
                                    @click="
                                        editData.id = '{{ $sd->id }}';
                                        editData.nama = '{{ $sd->nama }}';
                                        editData.budget = '{{ $sd->budget }}';
                                        editData.budget_harian = '{{ $sd->budget_harian }}';
                                        editModalOpen = true;
                                    "
                                    class="text-gray-400 hover:text-[#359FA0] p-1 text-xs transition-colors"
                                    title="Edit Pos"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('sumber-dana.destroy', $sd->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sumber dana ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 p-1 text-xs transition-colors" title="Hapus Pos">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Tag Aturan Khusus (Jika Uang Makan / Budget Harian) -->
                        @if($sd->budget_harian > 0)
                            <span class="inline-flex items-center space-x-1 bg-[#FFF0C5] text-[#359FA0] text-[10px] font-bold px-2.5 py-0.5 rounded-full mb-3">
                                <i class="fa-solid fa-clock-rotate-left text-[9px]"></i>
                                <span>Budget Harian: Rp{{ number_format($sd->budget_harian, 0, ',', '.') }}</span>
                            </span>
                        @endif

                        <!-- Nominal Saldo Aktif -->
                        <div class="my-2">
                            <p class="text-xs text-gray-400">Saldo Aktif Saat Ini:</p>
                            <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($sd->saldo_aktif, 0, ',', '.') }}</h3>
                        </div>

                        <!-- Progress Bar Dinamis -->
                        <div class="space-y-1.5 mt-3">
                            <div class="flex justify-between text-[11px] font-medium">
                                <span class="text-gray-400">Tingkat Kesehatan Kas</span>
                                <span class="font-bold {{ $sd->persentase <= 20 ? 'text-[#FF8C52]' : 'text-[#359FA0]' }}">
                                    {{ $sd->persentase }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div 
                                    class="h-2.5 rounded-full transition-all duration-500 {{ $sd->persentase <= 20 ? 'bg-[#FF8C52]' : 'bg-[#359FA0]' }}"
                                    style="width: {{ $sd->persentase }}%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Detail Pemasukan vs Pengeluaran -->
                    <div class="pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <p class="text-[10px] text-gray-400">Total Masuk</p>
                            <p class="font-semibold text-emerald-600">+Rp{{ number_format($sd->total_pemasukan, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400">Total Keluar</p>
                            <p class="font-semibold text-gray-700">-Rp{{ number_format($sd->total_pengeluaran, 0, ',', '.') }}</p>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full bg-white p-8 rounded-2xl border border-gray-100 text-center">
                    <i class="fa-solid fa-wallet text-gray-300 text-4xl mb-3"></i>
                    <p class="text-sm font-semibold text-gray-600">Belum ada sumber dana terdaftar.</p>
                    <p class="text-xs text-gray-400 mt-1">Klik tombol di atas untuk menambahkan pos keuangan baru.</p>
                </div>
            @endforelse
        </div>

        <!-- MODAL 1: CREATE SUMBER DANA -->
        <div 
            x-show="createModalOpen" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="createModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Tambah Sumber Dana</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('sumber-dana.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Sumber Dana</label>
                        <input type="text" name="nama" placeholder="Contoh: Uang Darurat / Dompet Digital" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Budget / Saldo Awal (Rp)</label>
                        <input type="number" name="budget" placeholder="0" min="0" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Budget Harian (Khusus Uang Makan, Opsional)</label>
                        <input type="number" name="budget_harian" placeholder="Contoh: 40000" min="0" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Sumber Dana</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL 2: EDIT SUMBER DANA -->
        <div 
            x-show="editModalOpen" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Edit Sumber Dana</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form :action="'/sumber-dana/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Sumber Dana</label>
                        <input type="text" name="nama" x-model="editData.nama" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Budget / Saldo Awal (Rp)</label>
                        <input type="number" name="budget" x-model="editData.budget" min="0" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Budget Harian (Opsional)</label>
                        <input type="number" name="budget_harian" x-model="editData.budget_harian" min="0" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Perbarui Sumber Dana</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>