<x-app-layout>
    <x-slot name="header">
        Pengeluaran Harian
    </x-slot>

    <div class="space-y-6" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        isBarangEdit: false,
        editData: { id: null, tanggal: '', jumlah: '', sumber_dana_id: '', keterangan: '', jenis_barang: '', tempat_beli: '' } 
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

        <!-- TOP CARDS INSIGHT UANG MAKAN & STATS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Pengeluaran Bulan Ini -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Total Pengeluaran Bulan Ini</span>
                    <i class="fa-solid fa-money-bill-transfer text-[#FF8C52]"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($stats['total_pengeluaran'], 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-400 mt-2">Seluruh Sumber Dana</p>
            </div>

            <!-- Card 2: Uang Makan Rata-Rata Harian -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                    <span>Rata-Rata Uang Makan</span>
                    <i class="fa-solid fa-utensils text-[#FFF0C5]"></i>
                </div>
                <h3 class="text-2xl font-bold text-[#FFF0C5]">Rp{{ number_format($stats['uang_makan_stats']['avg_harian'], 0, ',', '.') }} <span class="text-xs font-normal text-white/80">/ hari</span></h3>
                <p class="text-[10px] text-[#8AD6D1] mt-2">Benchmark: Rp40.000 / Hari</p>
            </div>

            <!-- Card 3: Uang Makan Terbesar -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Pengeluaran Makan Terbesar</span>
                    <i class="fa-solid fa-arrow-trend-up text-red-500"></i>
                </div>
                @if($stats['uang_makan_stats']['max_day'])
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['uang_makan_stats']['max_day']['nominal'], 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-red-500 font-medium mt-2"><i class="fa-regular fa-calendar"></i> {{ $stats['uang_makan_stats']['max_day']['tanggal'] }}</p>
                @else
                    <p class="text-xs text-gray-400">Belum ada data</p>
                @endif
            </div>

            <!-- Card 4: Uang Makan Terkecil -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Pengeluaran Makan Terkecil</span>
                    <i class="fa-solid fa-arrow-trend-down text-emerald-500"></i>
                </div>
                @if($stats['uang_makan_stats']['min_day'])
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['uang_makan_stats']['min_day']['nominal'], 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-emerald-600 font-medium mt-2"><i class="fa-regular fa-calendar"></i> {{ $stats['uang_makan_stats']['min_day']['tanggal'] }}</p>
                @else
                    <p class="text-xs text-gray-400">Belum ada data</p>
                @endif
            </div>

        </div>

        <!-- FILTER TOOLBAR & ACTION BUTTON -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
            
            <!-- Filter Form -->
            <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[180px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengeluaran..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div class="min-w-[140px]">
                    <select name="sumber_dana_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                        <option value="">Semua Sumber Dana</option>
                        @foreach($sumberDanaList as $sd)
                            <option value="{{ $sd->id }}" {{ request('sumber_dana_id') == $sd->id ? 'selected' : '' }}>{{ $sd->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold text-xs rounded-xl shadow-sm transition-all flex items-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'sumber_dana_id', 'start_date', 'end_date']))
                    <a href="{{ route('pengeluaran.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold text-xs rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Button Create -->
            <button 
                @click="createModalOpen = true"
                class="px-5 py-2.5 bg-[#FF8C52] hover:bg-[#e07740] active:scale-98 text-white font-semibold text-xs rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Catat Pengeluaran Baru</span>
            </button>
        </div>

        <!-- TABEL RIWAYAT PENGELUARAN -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Keterangan</th>
                            <th class="py-3.5 px-4">Sumber Dana</th>
                            <th class="py-3.5 px-4">Nominal</th>
                            <th class="py-3.5 px-4">Integrasi Aset/Investasi</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pengeluaranList as $p)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-gray-800 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-gray-800">
                                    {{ $p->keterangan }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="bg-[#8AD6D1]/20 text-[#359FA0] font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        {{ $p->sumberDana ? $p->sumberDana->nama : 'Umum' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap">
                                    Rp{{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($p->pencatatanInvestasi)
                                        <span class="bg-purple-100 text-purple-700 font-semibold px-2 py-0.5 rounded-md text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-chart-line text-[9px]"></i> Investasi
                                        </span>
                                    @endif

                                    @if($p->barang)
                                        <span class="bg-amber-100 text-amber-800 font-semibold px-2 py-0.5 rounded-md text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-box text-[9px]"></i> Barang ({{ $p->barang->jenis_barang == 'barang_mati' ? 'Mati' : 'Hidup' }})
                                        </span>
                                    @endif

                                    @if(!$p->pencatatanInvestasi && !$p->barang)
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button 
                                            @click="
                                                editData.id = '{{ $p->id }}';
                                                editData.tanggal = '{{ $p->tanggal->format('Y-m-d') }}';
                                                editData.jumlah = '{{ $p->jumlah }}';
                                                editData.sumber_dana_id = '{{ $p->sumber_dana_id }}';
                                                editData.keterangan = '{{ $p->keterangan }}';
                                                editModalOpen = true;
                                            "
                                            class="text-gray-400 hover:text-[#359FA0] p-1.5 transition-colors"
                                            title="Edit Transaksi"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('pengeluaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi pengeluaran ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 p-1.5 transition-colors" title="Hapus Transaksi">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 text-gray-300"></i>
                                    <p>Belum ada data pengeluaran.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="p-4 border-t border-gray-100">
                {{ $pengeluaranList->withQueryString()->links() }}
            </div>
        </div>

        <!-- MODAL CREATE PENGELUARAN -->
        <div 
            x-show="createModalOpen" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="createModalOpen = false" x-data="{ isBarangCreate: false }">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Catat Pengeluaran Baru</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('pengeluaran.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nominal (Rp)</label>
                        <input type="number" name="jumlah" placeholder="0" min="1" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Dana</label>
                        <select name="sumber_dana_id" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            @foreach($sumberDanaList as $sd)
                                <option value="{{ $sd->id }}">{{ $sd->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Nasi Goreng / Beli Laptop" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <!-- TOGGLE CATAT BARANG/ASET -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_barang" value="1" x-model="isBarangCreate" class="w-4 h-4 text-[#359FA0] rounded border-gray-300 focus:ring-[#359FA0]">
                            <span class="text-xs font-medium text-gray-700">Catat sebagai Barang / Aset?</span>
                        </label>

                        <div x-show="isBarangCreate" class="mt-3 space-y-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
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

                    <button type="submit" class="w-full py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Pengeluaran</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT PENGELUARAN -->
        <div 
            x-show="editModalOpen" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Edit Pengeluaran</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form :action="'/pengeluaran/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nominal (Rp)</label>
                        <input type="number" name="jumlah" x-model="editData.jumlah" min="1" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Dana</label>
                        <select name="sumber_dana_id" x-model="editData.sumber_dana_id" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            @foreach($sumberDanaList as $sd)
                                <option value="{{ $sd->id }}">{{ $sd->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                        <input type="text" name="keterangan" x-model="editData.keterangan" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Perbarui Pengeluaran</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>