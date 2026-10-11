<x-app-layout>
    <x-slot name="header">
        Inventaris Barang & Aset
    </x-slot>

    <div class="space-y-6" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        editData: { id: null, nama_barang: '', jenis_barang: 'barang_mati', harga: '', tempat_beli: '', tanggal_beli: '' }
    }">

        <!-- NOTIFIKASI SUCCESS -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl flex items-center justify-between text-xs font-semibold">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- TOP CARDS SUMMARY -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Estimasi Nilai Barang -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                    <span>Total Value Inventaris</span>
                    <i class="fa-solid fa-boxes-stacked text-[#FFF0C5]"></i>
                </div>
                <h3 class="text-2xl font-bold text-[#FFF0C5]">Rp{{ number_format($stats['total_nilai'], 0, ',', '.') }}</h3>
                <p class="text-[10px] text-[#8AD6D1] mt-2">Akumulasi Seluruh Barang & Aset</p>
            </div>

            <!-- Card 2: Count Barang Mati -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Total Barang Mati</span>
                    <i class="fa-solid fa-box text-amber-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['count_barang_mati'] }} <span class="text-xs font-normal text-gray-400">Item</span></h3>
                <p class="text-[10px] text-gray-400 mt-2">Item Konsumtif & Operasional</p>
            </div>

            <!-- Card 3: Count Barang Hidup / Aset -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Barang Hidup / Aset</span>
                    <i class="fa-solid fa-seedling text-emerald-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $stats['count_barang_hidup'] }} <span class="text-xs font-normal text-gray-400">Item</span></h3>
                <p class="text-[10px] text-gray-400 mt-2">Item Produktif & Aset Hidup</p>
            </div>

            <!-- Card 4: Pembelian Bulan Ini -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Pembelian Bulan Ini</span>
                    <i class="fa-solid fa-cart-shopping text-[#FF8C52]"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($stats['pembelian_bulan_ini'], 0, ',', '.') }}</h3>
                <p class="text-[10px] text-gray-400 mt-2">Nominal Barang Dibeli Bulan Ini</p>
            </div>

        </div>

        <!-- FILTER TOOLBAR & ACTION BUTTON -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
            
            <!-- Form Filter -->
            <form action="{{ route('barang.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[180px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / tempat beli..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div class="min-w-[150px]">
                    <select name="jenis_barang" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                        <option value="">Semua Jenis Barang</option>
                        <option value="barang_mati" {{ request('jenis_barang') == 'barang_mati' ? 'selected' : '' }}>Barang Mati</option>
                        <option value="barang_hidup" {{ request('jenis_barang') == 'barang_hidup' ? 'selected' : '' }}>Barang Hidup / Aset</option>
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-[#359FA0] text-white font-semibold text-xs rounded-xl transition-all flex items-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'jenis_barang']))
                    <a href="{{ route('barang.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Button Create Manual -->
            <button 
                @click="createModalOpen = true"
                class="px-5 py-2.5 bg-[#FF8C52] hover:bg-[#e07740] active:scale-98 text-white font-semibold text-xs rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Barang Manual</span>
            </button>
        </div>

        <!-- TABEL KATALOG INVENTARIS BARANG -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-4">Nama Barang</th>
                            <th class="py-3.5 px-4">Jenis Barang</th>
                            <th class="py-3.5 px-4">Harga / Value</th>
                            <th class="py-3.5 px-4">Tempat Beli</th>
                            <th class="py-3.5 px-4">Tgl Beli</th>
                            <th class="py-3.5 px-4">Sumber Input</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($barangList as $b)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-gray-800">
                                    {{ $b->nama_barang }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($b->jenis_barang == 'barang_mati')
                                        <span class="bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-box text-[9px]"></i> Barang Mati
                                        </span>
                                    @else
                                        <span class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-seedling text-[9px]"></i> Barang Hidup / Aset
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap">
                                    Rp{{ number_format($b->harga, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-600 whitespace-nowrap">
                                    {{ $b->tempat_beli ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($b->tanggal_beli)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($b->pengeluaran_id)
                                        <span class="bg-[#8AD6D1]/20 text-[#359FA0] font-semibold px-2 py-0.5 rounded-md text-[10px] inline-flex items-center gap-1" title="Otomatis dicatat dari Pengeluaran Harian">
                                            <i class="fa-solid fa-link text-[9px]"></i> Pengeluaran
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 font-semibold px-2 py-0.5 rounded-md text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-user-pen text-[9px]"></i> Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button 
                                            @click="
                                                editData.id = '{{ $b->id }}';
                                                editData.nama_barang = '{{ $b->nama_barang }}';
                                                editData.jenis_barang = '{{ $b->jenis_barang }}';
                                                editData.harga = '{{ $b->harga }}';
                                                editData.tempat_beli = '{{ $b->tempat_beli }}';
                                                editData.tanggal_beli = '{{ \Carbon\Carbon::parse($b->tanggal_beli)->format('Y-m-d') }}';
                                                editModalOpen = true;
                                            "
                                            class="text-gray-400 hover:text-[#359FA0] p-1.5 transition-colors"
                                            title="Edit Barang"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('barang.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini dari inventaris?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 p-1.5 transition-colors" title="Hapus Barang">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-gray-300"></i>
                                    <p>Belum ada barang terdaftar di inventaris.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="p-4 border-t border-gray-100">
                {{ $barangList->withQueryString()->links() }}
            </div>
        </div>

        <!-- MODAL CREATE BARANG MANUAL -->
        <div 
            x-show="createModalOpen" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="createModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Tambah Barang Manual</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('barang.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" placeholder="Contoh: Laptop Asus / Kambing Ternak" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Barang</label>
                        <select name="jenis_barang" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            <option value="barang_mati">Barang Mati (Konsumtif/Operasional)</option>
                            <option value="barang_hidup">Barang Hidup / Aset Produktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga / Estimasi Nilai (Rp)</label>
                        <input type="number" name="harga" placeholder="0" min="0" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tempat Beli (Opsional)</label>
                            <input type="text" name="tempat_beli" placeholder="Tokopedia, Shopee..." class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Barang</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT BARANG -->
        <div 
            x-show="editModalOpen" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Edit Data Barang</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <form :action="'/barang/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" x-model="editData.nama_barang" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Barang</label>
                        <select name="jenis_barang" x-model="editData.jenis_barang" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            <option value="barang_mati">Barang Mati</option>
                            <option value="barang_hidup">Barang Hidup / Aset</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga / Nilai (Rp)</label>
                        <input type="number" name="harga" x-model="editData.harga" min="0" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" x-model="editData.tanggal_beli" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tempat Beli</label>
                            <input type="text" name="tempat_beli" x-model="editData.tempat_beli" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Perbarui Barang</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>