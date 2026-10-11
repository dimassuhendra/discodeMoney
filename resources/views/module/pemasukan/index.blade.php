<x-app-layout>
    <x-slot name="header">
        Kelola Pemasukan
    </x-slot>

    <div class="space-y-6" x-data="{ 
        createModalOpen: false, 
        editModalOpen: false, 
        editData: { id: null, keterangan: '', sumber_dana_id: '', jumlah: '', tanggal: '' }
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
            
            <!-- Card 1: Pemasukan Bulan Ini -->
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                    <span>Pemasukan Bulan Ini</span>
                    <i class="fa-solid fa-wallet text-[#FFF0C5]"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-[#FFF0C5]">Rp{{ number_format($stats['bulan_ini'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-[#8AD6D1] mt-1 font-semibold">Total Arus Kas Masuk Bulan Ini</p>
                </div>
            </div>

            <!-- Card 2: Total Terfilter -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Total Pemasukan (Filter)</span>
                    <i class="fa-solid fa-[#359FA0] fa-money-bill-wave"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Rp{{ number_format($stats['total_filtered'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">Sesuai Filter / Seluruh Transaksi</p>
                </div>
            </div>

            <!-- Card 3: Target Rekening Dominan -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Penerima Terbesar</span>
                    <i class="fa-solid fa-building-columns text-amber-500"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 truncate">{{ $stats['top_sumber_dana'] }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1">Wallet Paling Banyak Menerima</p>
                </div>
            </div>

            <!-- Card 4: Total Pemasukan YTD -->
            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                    <span>Pemasukan Tahun Ini (YTD)</span>
                    <i class="fa-solid fa-chart-line text-[#FF8C52]"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($stats['pemasukan_ytd'], 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">Akumulasi Sejak Awal Tahun</p>
                </div>
            </div>

        </div>

        <!-- CHART SECTION: TREND PEMASUKAN BULANAN -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h4 class="font-bold text-gray-800 text-sm mb-4">Trend Pemasukan Bulanan</h4>
            <div class="h-60 relative">
                <canvas id="trendPemasukanChart"></canvas>
            </div>
        </div>

        <!-- FILTER TOOLBAR & ACTION BUTTON -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
            
            <form action="{{ route('pemasukan.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[160px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan..." class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div class="min-w-[140px]">
                    <select name="sumber_dana_id" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                        <option value="">Semua Wallet</option>
                        @foreach($sumberDanaList as $sd)
                            <option value="{{ $sd->id }}" {{ request('sumber_dana_id') == $sd->id ? 'selected' : '' }}>{{ $sd->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-1.5">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                    <span class="text-gray-400 text-xs">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-2.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <button type="submit" class="px-4 py-2 bg-[#359FA0] text-white font-semibold text-xs rounded-xl transition-all flex items-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'sumber_dana_id', 'start_date', 'end_date']))
                    <a href="{{ route('pemasukan.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </form>

            <button 
                @click="createModalOpen = true"
                class="px-5 py-2.5 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold text-xs rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Pemasukan</span>
            </button>
        </div>

        <!-- TABEL KATALOG PEMASUKAN -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-4">Tanggal</th>
                            <th class="py-3.5 px-4">Keterangan</th>
                            <th class="py-3.5 px-4">Masuk Ke Wallet</th>
                            <th class="py-3.5 px-4">Jumlah (Rp)</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pemasukanList as $p)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-3.5 px-4 whitespace-nowrap font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-800">
                                    {{ $p->keterangan }}
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-gray-700 whitespace-nowrap">
                                    <span class="bg-[#8AD6D1]/20 text-[#359FA0] font-bold px-2.5 py-1 rounded-full text-[10px]">
                                        <i class="fa-solid fa-wallet text-[9px] mr-1"></i>
                                        {{ $p->sumberDana->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-emerald-600 whitespace-nowrap text-sm">
                                    +Rp{{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button 
                                            @click="
                                                editData.id = '{{ $p->id }}';
                                                editData.keterangan = '{{ $p->keterangan }}';
                                                editData.sumber_dana_id = '{{ $p->sumber_dana_id }}';
                                                editData.jumlah = '{{ $p->jumlah }}';
                                                editData.tanggal = '{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }}';
                                                editModalOpen = true;
                                            "
                                            class="text-gray-400 hover:text-[#359FA0] p-1.5 transition-colors"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('pemasukan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi pemasukan ini?');">
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
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-hand-holding-dollar text-3xl mb-2 text-gray-300"></i>
                                    <p>Belum ada transaksi pemasukan yang dicatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                {{ $pemasukanList->withQueryString()->links() }}
            </div>
        </div>

        <!-- MODAL CREATE PEMASUKAN -->
        <div x-show="createModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="createModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Catat Pemasukan Baru</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form action="{{ route('pemasukan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Terima</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan / Deskripsi</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Gaji Bulan Ini, DP Project Website" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Masuk Ke Rekening / Wallet</label>
                        <select name="sumber_dana_id" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            @foreach($sumberDanaList as $sd)
                                <option value="{{ $sd->id }}">{{ $sd->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nominal Pemasukan (Rp)</label>
                        <input type="number" name="jumlah" placeholder="0" min="1" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Pemasukan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT PEMASUKAN -->
        <div x-show="editModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Edit Pemasukan</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form :action="'/pemasukan/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Terima</label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan / Deskripsi</label>
                        <input type="text" name="keterangan" x-model="editData.keterangan" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Masuk Ke Rekening / Wallet</label>
                        <select name="sumber_dana_id" x-model="editData.sumber_dana_id" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            @foreach($sumberDanaList as $sd)
                                <option value="{{ $sd->id }}">{{ $sd->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nominal Pemasukan (Rp)</label>
                        <input type="number" name="jumlah" x-model="editData.jumlah" min="1" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Perbarui Pemasukan</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- SCRIPT CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctxBar = document.getElementById('trendPemasukanChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($stats['chart_bar']['labels']) !!},
                    datasets: [{
                        label: 'Total Pemasukan',
                        data: {!! json_encode($stats['chart_bar']['values']) !!},
                        backgroundColor: '#359fa0',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(item) {
                                    return 'Total: Rp' + item.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
</x-app-layout>