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
            
            <!-- Card 1: Total Aset & Barang -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                    <span>Total Aset</span>
                    <i class="fa-solid fa-boxes-stacked text-[#FFF0C5]"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-[#FFF0C5]">Rp{{ number_format($stats['total_nilai'], 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-[#8AD6D1] mt-1 font-semibold">{{ $stats['total_count'] }} Total Item Terdaftar</p>
                </div>
            </div>

            <!-- Card 2: Barang Mati -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Barang Mati</span>
                    <i class="fa-solid fa-box text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['mati']['total'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">{{ $stats['mati']['count'] }} Item Operasional</p>
                </div>
            </div>

            <!-- Card 3: Barang Hidup / Aset -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Barang Hidup / Aset</span>
                    <i class="fa-solid fa-seedling text-emerald-500"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['hidup']['total'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">{{ $stats['hidup']['count'] }} Item Produktif</p>
                </div>
            </div>

            <!-- Card 4: Kebutuhan Rutin -->
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

        <!-- CHARTS SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Pie Chart: Persentase Jenis Barang -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm lg:col-span-1">
                <h4 class="font-bold text-gray-800 text-sm mb-4">Persentase Jenis Barang</h4>
                <div class="h-56 relative">
                    <canvas id="jenisBarangChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Trend Pembelian Bulanan + Sum -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm lg:col-span-2">
                <h4 class="font-bold text-gray-800 text-sm mb-4">Trend Pembelian Bulanan</h4>
                <div class="h-56 relative">
                    <canvas id="trendMonthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- FILTER TOOLBAR (SEARCH + DATE RANGE) -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
            
            <form action="{{ route('barang.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[160px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / tempat beli..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div class="min-w-[130px]">
                    <select name="jenis_barang" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                        <option value="">Semua Jenis</option>
                        <option value="barang_mati" {{ request('jenis_barang') == 'barang_mati' ? 'selected' : '' }}>Barang Mati</option>
                        <option value="barang_hidup" {{ request('jenis_barang') == 'barang_hidup' ? 'selected' : '' }}>Barang Hidup / Aset</option>
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
                                <!-- Klik nama barang untuk lihat agregasi histori item -->
                                <td class="py-3.5 px-4 font-bold text-[#359FA0] hover:underline cursor-pointer" @click="openDetailItem('{{ $b->nama_barang }}')">
                                    <i class="fa-solid fa-circle-info text-[11px] mr-1 text-[#8AD6D1]"></i>
                                    {{ $b->nama_barang }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($b->jenis_barang == 'barang_mati')
                                        <span class="bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                            <i class="fa-solid fa-box text-[9px] mr-0.5"></i> Barang Mati
                                        </span>
                                    @elseif($b->jenis_barang == 'barang_hidup')
                                        <span class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                            <i class="fa-solid fa-seedling text-[9px] mr-0.5"></i> Barang Hidup / Aset
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
            // 1. Doughnut Chart: Persentase Jenis Barang
            const ctxPie = document.getElementById('jenisBarangChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                plugins: [ChartDataLabels], // <-- Aktifkan plugin datalabels
                data: {
                    labels: ['Barang Mati', 'Barang Hidup / Aset', 'Kebutuhan Rutin'],
                    datasets: [{
                        data: [
                            {{ $stats['chart_pie']['barang_mati'] }},
                            {{ $stats['chart_pie']['barang_hidup'] }},
                            {{ $stats['chart_pie']['kebutuhan_rutin'] }}
                        ],
                        backgroundColor: ['#1e3e3f', '#359FA0', '#FF8C52'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'bottom', 
                            labels: { font: { size: 10 } } 
                        },
                        // Konfigurasi Tampilan Persentase di Atas Slices Chart
                        datalabels: {
                            color: '#ffffff',
                            font: {
                                weight: 'bold',
                                size: 11
                            },
                            formatter: (value, ctx) => {
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.map(data => { sum += Number(data); });
                                if (sum === 0) return '0%';
                                let percentage = (value * 100 / sum).toFixed(1) + "%";
                                return percentage;
                            }
                        }
                    }
                }
            });
            // 2. Grouped Bar Chart: Trend Pembelian Bulanan + Sum
            const ctxBar = document.getElementById('trendMonthlyChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($stats['chart_bar']['labels']) !!},
                    datasets: [
                        {
                            label: 'Barang Mati',
                            data: {!! json_encode($stats['chart_bar']['barang_mati']) !!},
                            backgroundColor: '#1e3e3f'
                        },
                        {
                            label: 'Barang Hidup / Aset',
                            data: {!! json_encode($stats['chart_bar']['barang_hidup']) !!},
                            backgroundColor: '#359FA0'
                        },
                        {
                            label: 'Kebutuhan Rutin',
                            data: {!! json_encode($stats['chart_bar']['kebutuhan_rutin']) !!},
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
                                    tooltipItems.forEach(function(tooltipItem) {
                                        sum += tooltipItem.parsed.y;
                                    });
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
    </script>
</x-app-layout>