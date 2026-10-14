<x-app-layout>
    <x-slot name="header">
        Inventaris Barang & Aset
    </x-slot>

    <div class="space-y-6" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        detailModalOpen: false,
        detailLoading: false,
        detailData: { nama_barang: '', total_dibeli: 0, total_pengeluaran: 0, rata_rata_bulanan: 0, history_list: [] },
        editData: { id: null, nama_barang: '', jenis_barang: 'barang_mati', harga: '', tempat_beli: '', tanggal_beli: '' },

        openDetailItem(namaBarang) {
            this.detailModalOpen = true;
            this.detailLoading = true;
            fetch(`/barang/history-by-nama?nama=${encodeURIComponent(namaBarang)}`)
                .then(res => res.json())
                .then(data => {
                    this.detailData = data;
                    this.detailLoading = false;
                });
        }
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
            <!-- Total Nilai Inventaris -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                    <span>Total Nilai Inventaris</span>
                    <i class="fa-solid fa-boxes-stacked text-[#FFF0C5]"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-[#FFF0C5]">Rp{{ number_format($stats['total_nilai'], 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-[#8AD6D1] mt-1 font-semibold">{{ $stats['total_count'] }} Total Item Terdaftar</p>
                </div>
            </div>

            <!-- Barang Operasional -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Barang Operasional</span>
                    <i class="fa-solid fa-box text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['operasional']['total'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">{{ $stats['operasional']['count'] }} Item Operasional</p>
                </div>
            </div>

            <!-- Barang Produktif -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Barang Produktif</span>
                    <i class="fa-solid fa-seedling text-emerald-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['produktif']['total'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">{{ $stats['produktif']['count'] }} Item Produktif</p>
                </div>
            </div>

            <!-- Kebutuhan Rutin -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Kebutuhan Rutin</span>
                    <i class="fa-solid fa-repeat text-[#FF8C52]"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['rutin']['total'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">{{ $stats['rutin']['count'] }} Item Rutin/Sembako</p>
                </div>
            </div>
        </div>

        <!-- INSIGHT & HIGHLIGHT CARDS (AVG & MAX) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Avg Barang Operasional -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-amber-50 rounded-xl text-amber-600">
                        <i class="fa-solid fa-calculator text-base"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-medium">Avg Barang Operasional</p>
                        <h4 class="text-sm font-bold text-gray-800">Rp{{ number_format($stats['avg_barang_operasional'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <!-- Avg Barang Produktif -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600">
                        <i class="fa-solid fa-chart-line text-base"></i>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 font-medium">Avg Barang Produktif</p>
                        <h4 class="text-sm font-bold text-gray-800">Rp{{ number_format($stats['avg_barang_produktif'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            <!-- Barang Produktif Terbesar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-blue-50 rounded-xl text-blue-600">
                        <i class="fa-solid fa-arrow-trend-up text-base"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[11px] text-gray-400 font-medium truncate">Barang Produktif Terbesar</p>
                        <h4 class="text-sm font-bold text-gray-800">
                            {{ $stats['max_barang_produktif'] ? 'Rp' . number_format($stats['max_barang_produktif']->harga, 0, ',', '.') : '-' }}
                        </h4>
                        <p class="text-[9px] text-gray-400 truncate">{{ $stats['max_barang_produktif']->nama_barang ?? 'Belum ada data' }}</p>
                    </div>
                </div>
            </div>

            <!-- Barang Operasional Terbesar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-purple-50 rounded-xl text-purple-600">
                        <i class="fa-solid fa-award text-base"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[11px] text-gray-400 font-medium truncate">Barang Operasional Terbesar</p>
                        <h4 class="text-sm font-bold text-gray-800">
                            {{ $stats['max_barang_operasional'] ? 'Rp' . number_format($stats['max_barang_operasional']->harga, 0, ',', '.') : '-' }}
                        </h4>
                        <p class="text-[9px] text-gray-400 truncate">{{ $stats['max_barang_operasional']->nama_barang ?? 'Belum ada data' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION 1: DUAL DONUT CHARTS & TREND PEMBELIAN BULANAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Donut Chart 1: Persentase Aset (All Time - Operasional vs Produktif) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm lg:col-span-1 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-xs mb-1">Persentase Aset Dimiliki</h4>
                    <p class="text-[10px] text-gray-400 mb-4">Kumulatif All-Time (Operasional vs Produktif)</p>
                </div>
                <div class="h-48 relative">
                    <canvas id="chartPieAset"></canvas>
                </div>
            </div>

            <!-- Donut Chart 2: Persentase Pembelian Bulan Ini (MTD) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm lg:col-span-1 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-xs mb-1">Pembelian Bulan Ini (MTD)</h4>
                    <p class="text-[10px] text-gray-400 mb-4">Alokasi Pembelian Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                </div>
                <div class="h-48 relative">
                    <canvas id="chartPieMtd"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Trend Pembelian Bulanan -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm lg:col-span-1 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-xs mb-1">Trend Pembelian Bulanan</h4>
                    <p class="text-[10px] text-gray-400 mb-4">Total Pengeluaran per Bulan</p>
                </div>
                <div class="h-48 relative">
                    <canvas id="trendMonthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- CHARTS SECTION 2: 4 LINE CHARTS PERTUMBUHAN DENGAN ALPINE SWITCHERS (GRID 2x2) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. Pertumbuhan Barang Produktif -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm" x-data="{ mode: 'monthly' }">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs">Pertumbuhan Barang Produktif</h4>
                        <p class="text-[10px] text-gray-400">Tren Akumulasi Aset Produktif</p>
                    </div>
                    <div class="bg-gray-100 p-0.5 rounded-lg text-[10px] flex space-x-1">
                        <button @click="mode = 'monthly'; updateGrowthChart('chartProduktif', 'monthly')" 
                                :class="mode === 'monthly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Bulan</button>
                        <button @click="mode = 'yearly'; updateGrowthChart('chartProduktif', 'yearly')" 
                                :class="mode === 'yearly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Tahun</button>
                    </div>
                </div>
                <div class="h-48 relative">
                    <canvas id="chartBarangProduktif"></canvas>
                </div>
            </div>

            <!-- 2. Pertumbuhan Barang Operasional -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm" x-data="{ mode: 'monthly' }">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs">Pertumbuhan Barang Operasional</h4>
                        <p class="text-[10px] text-gray-400">Tren Akumulasi Barang Operasional</p>
                    </div>
                    <div class="bg-gray-100 p-0.5 rounded-lg text-[10px] flex space-x-1">
                        <button @click="mode = 'monthly'; updateGrowthChart('chartOperasional', 'monthly')" 
                                :class="mode === 'monthly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Bulan</button>
                        <button @click="mode = 'yearly'; updateGrowthChart('chartOperasional', 'yearly')" 
                                :class="mode === 'yearly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Tahun</button>
                    </div>
                </div>
                <div class="h-48 relative">
                    <canvas id="chartBarangOperasional"></canvas>
                </div>
            </div>

            <!-- 3. Pertumbuhan Kebutuhan Rutin -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm" x-data="{ mode: 'monthly' }">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs">Pertumbuhan Kebutuhan Rutin</h4>
                        <p class="text-[10px] text-gray-400">Tren Akumulasi Kebutuhan Rutin</p>
                    </div>
                    <div class="bg-gray-100 p-0.5 rounded-lg text-[10px] flex space-x-1">
                        <button @click="mode = 'monthly'; updateGrowthChart('chartRutin', 'monthly')" 
                                :class="mode === 'monthly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Bulan</button>
                        <button @click="mode = 'yearly'; updateGrowthChart('chartRutin', 'yearly')" 
                                :class="mode === 'yearly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Tahun</button>
                    </div>
                </div>
                <div class="h-48 relative">
                    <canvas id="chartKebutuhanRutin"></canvas>
                </div>
            </div>

            <!-- 4. Pertumbuhan Total Inventaris -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm" x-data="{ mode: 'monthly' }">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-gray-800 text-xs">Pertumbuhan Total Inventaris</h4>
                        <p class="text-[10px] text-gray-400">Gabungan Operasional & Produktif</p>
                    </div>
                    <div class="bg-gray-100 p-0.5 rounded-lg text-[10px] flex space-x-1">
                        <button @click="mode = 'monthly'; updateGrowthChart('chartTotal', 'monthly')" 
                                :class="mode === 'monthly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Bulan</button>
                        <button @click="mode = 'yearly'; updateGrowthChart('chartTotal', 'yearly')" 
                                :class="mode === 'yearly' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500'" 
                                class="px-2 py-0.5 rounded-md transition-all font-semibold">Tahun</button>
                    </div>
                </div>
                <div class="h-48 relative">
                    <canvas id="chartTotalInventaris"></canvas>
                </div>
            </div>
        </div>

        <!-- FILTER TOOLBAR -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
            <form action="{{ route('barang.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[160px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / tempat beli..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div class="min-w-[130px]">
                    <select name="jenis_barang" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                        <option value="">Semua Jenis</option>
                        <option value="barang_mati" {{ request('jenis_barang') == 'barang_mati' ? 'selected' : '' }}>Barang Operasional</option>
                        <option value="barang_hidup" {{ request('jenis_barang') == 'barang_hidup' ? 'selected' : '' }}>Barang Produktif</option>
                        <option value="kebutuhan_rutin" {{ request('jenis_barang') == 'kebutuhan_rutin' ? 'selected' : '' }}>Kebutuhan Rutin</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="flex items-center space-x-1.5">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                    <span class="text-gray-400 text-xs">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <button type="submit" class="px-4 py-2 bg-[#359FA0] text-white font-semibold text-xs rounded-xl transition-all flex items-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'jenis_barang', 'start_date', 'end_date']))
                    <a href="{{ route('barang.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </form>

            <button 
                @click="createModalOpen = true"
                class="px-5 py-2.5 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold text-xs rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 whitespace-nowrap"
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
                                <td class="py-3.5 px-4 font-bold text-[#359FA0] hover:underline cursor-pointer" @click="openDetailItem('{{ $b->nama_barang }}')">
                                    <i class="fa-solid fa-circle-info text-[11px] mr-1 text-[#8AD6D1]"></i>
                                    {{ $b->nama_barang }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($b->jenis_barang == 'barang_mati')
                                        <span class="bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                            <i class="fa-solid fa-box text-[9px] mr-0.5"></i> Barang Operasional
                                        </span>
                                    @elseif($b->jenis_barang == 'barang_hidup')
                                        <span class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                            <i class="fa-solid fa-seedling text-[9px] mr-0.5"></i> Barang Produktif
                                        </span>
                                    @else
                                        <span class="bg-orange-100 text-orange-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                            <i class="fa-solid fa-repeat text-[9px] mr-0.5"></i> Kebutuhan Rutin
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
                                        <span class="bg-[#8AD6D1]/20 text-[#359FA0] font-semibold px-2 py-0.5 rounded-md text-[10px]">
                                            <i class="fa-solid fa-link text-[9px]"></i> Pengeluaran
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 font-semibold px-2 py-0.5 rounded-md text-[10px]">
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
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('barang.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini dari inventaris?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 p-1.5 transition-colors">
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

            <div class="p-4 border-t border-gray-100">
                {{ $barangList->withQueryString()->links() }}
            </div>
        </div>

        @include('module.barang._modal')

    </div>

    <!-- SCRIPT CHART.JS INITIALIZATION -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Config Plugin Datalabels
            const datalabelsConfig = {
                color: '#ffffff',
                font: { weight: 'bold', size: 10 },
                formatter: (value, ctx) => {
                    let sum = 0;
                    let dataArr = ctx.chart.data.datasets[0].data;
                    dataArr.map(data => { sum += Number(data); });
                    if (sum === 0) return '0%';
                    return (value * 100 / sum).toFixed(1) + "%";
                }
            };

            // 1. Donut Chart: Persentase Aset Dimiliki (All Time)
            const ctxPieAset = document.getElementById('chartPieAset').getContext('2d');
            new Chart(ctxPieAset, {
                type: 'doughnut',
                plugins: [ChartDataLabels],
                data: {
                    labels: ['Barang Operasional', 'Barang Produktif'],
                    datasets: [{
                        data: [
                            {{ $stats['chart_pie_aset']['barang_operasional'] }},
                            {{ $stats['chart_pie_aset']['barang_produktif'] }}
                        ],
                        backgroundColor: ['#1e3e3f', '#359FA0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 10 } } },
                        datalabels: datalabelsConfig
                    }
                }
            });

            // 2. Donut Chart: Pembelian Bulan Ini (MTD)
            const ctxPieMtd = document.getElementById('chartPieMtd').getContext('2d');
            new Chart(ctxPieMtd, {
                type: 'doughnut',
                plugins: [ChartDataLabels],
                data: {
                    labels: ['Barang Operasional', 'Barang Produktif', 'Kebutuhan Rutin'],
                    datasets: [{
                        data: [
                            {{ $stats['chart_pie_mtd']['barang_operasional'] }},
                            {{ $stats['chart_pie_mtd']['barang_produktif'] }},
                            {{ $stats['chart_pie_mtd']['kebutuhan_rutin'] }}
                        ],
                        backgroundColor: ['#1e3e3f', '#359FA0', '#FF8C52'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 10 } } },
                        datalabels: datalabelsConfig
                    }
                }
            });

            // 3. Grouped Bar Chart: Trend Pembelian Bulanan
            const ctxBar = document.getElementById('trendMonthlyChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($stats['growth_monthly']['labels']) !!},
                    datasets: [
                        {
                            label: 'Operasional',
                            data: {!! json_encode($stats['growth_monthly']['barang_operasional']) !!},
                            backgroundColor: '#1e3e3f'
                        },
                        {
                            label: 'Produktif',
                            data: {!! json_encode($stats['growth_monthly']['barang_produktif']) !!},
                            backgroundColor: '#359FA0'
                        },
                        {
                            label: 'Rutin',
                            data: {!! json_encode($stats['growth_monthly']['kebutuhan_rutin']) !!},
                            backgroundColor: '#FF8C52'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                footer: function(tooltipItems) {
                                    let sum = 0;
                                    tooltipItems.forEach(item => { sum += item.parsed.y; });
                                    return 'Total Bulan Ini: Rp' + sum.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true }
                    }
                }
            });
        });

        // 4 Line Charts Pertumbuhan (Growth)
        const growthMonthly = {!! json_encode($stats['growth_monthly']) !!};
        const growthYearly  = {!! json_encode($stats['growth_yearly']) !!};

        let chartInstances = {};

        function createLineChart(ctxId, label, dataMonthly, color) {
            const ctx = document.getElementById(ctxId).getContext('2d');
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dataMonthly.labels,
                    datasets: [{
                        label: label,
                        data: dataMonthly.data,
                        borderColor: color,
                        backgroundColor: color + '15',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' Rp' + ctx.parsed.y.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi 4 Line Charts Pertumbuhan
            chartInstances['chartProduktif'] = createLineChart('chartBarangProduktif', 'Barang Produktif', 
                { labels: growthMonthly.labels, data: growthMonthly.barang_produktif },
                '#359FA0'
            );

            chartInstances['chartOperasional'] = createLineChart('chartBarangOperasional', 'Barang Operasional', 
                { labels: growthMonthly.labels, data: growthMonthly.barang_operasional },
                '#1e3e3f'
            );

            chartInstances['chartRutin'] = createLineChart('chartKebutuhanRutin', 'Kebutuhan Rutin', 
                { labels: growthMonthly.labels, data: growthMonthly.kebutuhan_rutin },
                '#FF8C52'
            );

            chartInstances['chartTotal'] = createLineChart('chartTotalInventaris', 'Total Inventaris', 
                { labels: growthMonthly.labels, data: growthMonthly.total_inventaris },
                '#8B5CF6'
            );
        });

        // Handler Switch Mode (Bulan / Tahun) dari Alpine
        window.updateGrowthChart = function(chartKey, mode) {
            const chart = chartInstances[chartKey];
            if (!chart) return;

            const dataSource = mode === 'monthly' ? growthMonthly : growthYearly;
            let field = 'total_inventaris';
            if (chartKey === 'chartProduktif') field = 'barang_produktif';
            if (chartKey === 'chartOperasional') field = 'barang_operasional';
            if (chartKey === 'chartRutin') field = 'kebutuhan_rutin';

            chart.data.labels = dataSource.labels;
            chart.data.datasets[0].data = dataSource[field];
            chart.update();
        };
    </script>
</x-app-layout>