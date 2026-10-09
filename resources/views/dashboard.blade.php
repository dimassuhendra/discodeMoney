<x-app-layout>
    <x-slot name="header">
        Dashboard Utama
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6">

        <!-- BANNER DRAFT INVESTASI -->
        @if($draftInvestasiCount > 0)
            <div class="bg-gradient-to-r from-[#FF8C52] to-[#e07740] rounded-2xl p-4 md:p-5 text-white shadow-lg flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-lightbulb text-[#FFF0C5]"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base">Draft Investasi Perlu Dilengkapi</h4>
                        <p class="text-xs text-white/90">Ada {{ $draftInvestasiCount }} transaksi pengeluaran investasi yang belum dilengkapi detail platform / harga unitnya.</p>
                    </div>
                </div>
                <a href="#" class="px-4 py-2 bg-white text-[#FF8C52] hover:bg-gray-100 font-semibold text-xs rounded-xl shadow-sm transition-all whitespace-nowrap flex items-center space-x-1">
                    <span>Lengkapi Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        @endif

        <!-- WIDGET 1: BUDGET UANG MAKAN (KEMARIN, HARI INI, BESOK) -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-serif font-bold text-lg text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-utensils text-[#359FA0]"></i>
                    <span>Budget Uang Makan</span>
                </h2>
                <span class="text-xs text-gray-500">Benchmark: Rp40.000 / Hari</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Card 1: Kemarin -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                        <span>Kemarin ({{ $uangMakan['kemarin']['tanggal'] }})</span>
                        @if($uangMakan['kemarin']['is_overbudget'])
                            <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold text-[10px] flex items-center gap-1">
                                <i class="fa-solid fa-triangle-exclamation"></i> Overbudget
                            </span>
                        @else
                            <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full font-bold text-[10px] flex items-center gap-1">
                                <i class="fa-solid fa-circle-check"></i> Aman
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400">Terpakai:</p>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($uangMakan['kemarin']['pengeluaran'], 0, ',', '.') }}</h3>
                    <div class="mt-3 pt-3 border-t border-gray-50 text-xs text-gray-500">
                        @if($uangMakan['kemarin']['defisit'] > 0)
                            <span class="text-red-500 font-medium">Defisit -Rp{{ number_format($uangMakan['kemarin']['defisit'], 0, ',', '.') }} (memotong hari ini)</span>
                        @else
                            <span class="text-emerald-600">Tidak ada defisit</span>
                        @endif
                    </div>
                </div>

                <!-- Card 2: Hari Ini (Highlight Utama) -->
                <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md relative overflow-hidden">
                    <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                        <span>Hari Ini ({{ $uangMakan['hari_ini']['tanggal'] }})</span>
                        <span class="bg-[#FFF0C5] text-[#359FA0] px-2 py-0.5 rounded-full font-bold text-[10px]">Aktif</span>
                    </div>
                    <p class="text-xs text-white/80">Sisa Budget Hari Ini:</p>
                    <h3 class="text-3xl font-bold text-[#FFF0C5] mt-0.5">
                        Rp{{ number_format($uangMakan['hari_ini']['sisa_budget'], 0, ',', '.') }}
                    </h3>

                    <div class="mt-3 pt-3 border-t border-white/10 text-xs flex justify-between text-white/90">
                        <span>Budget Awal: Rp{{ number_format($uangMakan['hari_ini']['budget_awal'], 0, ',', '.') }}</span>
                        <span>Terpakai: Rp{{ number_format($uangMakan['hari_ini']['pengeluaran'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Card 3: Besok (Estimasi) -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                        <span>Besok ({{ $uangMakan['besok']['tanggal'] }})</span>
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-bold text-[10px]">Estimasi</span>
                    </div>
                    <p class="text-xs text-gray-400">Estimasi Budget Besok:</p>
                    <h3 class="text-xl font-bold text-gray-800">Rp{{ number_format($uangMakan['besok']['estimasi_budget'], 0, ',', '.') }}</h3>
                    <div class="mt-3 pt-3 border-t border-gray-50 text-xs text-gray-500">
                        @if($uangMakan['besok']['potongan_defisit_hari_ini'] > 0)
                            <span class="text-red-500 font-medium">Terpotong Rp{{ number_format($uangMakan['besok']['potongan_defisit_hari_ini'], 0, ',', '.') }} dari overbudget hari ini</span>
                        @else
                            <span class="text-emerald-600">Budget normal Rp40.000</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- WIDGET 2: RINGKASAN SALDO & CASHFLOW BULAN INI -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Ringkasan Performa Bulan Ini (In vs Out) -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-serif font-bold text-gray-800 mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-[#359FA0] fa-[#359FA0] fa-scale-balanced text-[#359FA0]"></i>
                        <span>Cashflow Bulan Ini</span>
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-hand-holding-dollar text-emerald-600 text-xl"></i>
                                <div>
                                    <p class="text-xs text-emerald-700 font-semibold">Pemasukan</p>
                                    <p class="text-sm font-bold text-emerald-900">Rp{{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-money-bill-transfer text-red-500 text-xl"></i>
                                <div>
                                    <p class="text-xs text-red-700 font-semibold">Pengeluaran</p>
                                    <p class="text-sm font-bold text-red-900">Rp{{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs text-gray-500 font-medium">Net Cashflow</span>
                    <span class="text-base font-bold {{ $netCashflow >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $netCashflow >= 0 ? '+' : '' }}Rp{{ number_format($netCashflow, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Breakdown Sumber Dana DENGAN PROGRESS BAR DINAMIS -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-serif font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fa-solid fa-vault text-[#359FA0]"></i>
                        <span>Saldo Sumber Dana</span>
                    </h3>
                    <span class="text-xs font-semibold bg-[#8AD6D1]/20 text-[#359FA0] px-3 py-1 rounded-full">
                        Total Kas: Rp{{ number_format($totalSaldoAktif, 0, ',', '.') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($sumberDanaList as $sd)
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl flex flex-col justify-between space-y-3">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-xs text-gray-500 font-medium">{{ $sd->nama }}</p>
                                    <span class="text-[10px] font-bold {{ $sd->persentase <= 20 ? 'text-[#FF8C52]' : 'text-[#359FA0]' }}">
                                        {{ $sd->persentase }}%
                                    </span>
                                </div>
                                <p class="text-lg font-bold text-gray-800">Rp{{ number_format($sd->saldo_aktif, 0, ',', '.') }}</p>
                            </div>

                            <!-- DYNAMIC PROGRESS BAR -->
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div 
                                    class="h-2 rounded-full transition-all duration-500 {{ $sd->persentase <= 20 ? 'bg-[#FF8C52]' : 'bg-[#359FA0]' }}" 
                                    style="width: {{ $sd->persentase }}%"
                                ></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Stats Barang / Aset Bulan Ini -->
                <div class="mt-6 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-box text-gray-400 text-lg"></i>
                        <div>
                            <p class="text-xs text-gray-400">Pembelian Barang Mati (Bulan Ini)</p>
                            <p class="text-sm font-bold text-gray-700">Rp{{ number_format($totalBarangMatiBulanIni, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-seedling text-emerald-500 text-lg"></i>
                        <div>
                            <p class="text-xs text-gray-400">Pembelian Barang Hidup/Aset (Bulan Ini)</p>
                            <p class="text-sm font-bold text-gray-700">Rp{{ number_format($totalBarangHidupBulanIni, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- WIDGET 3: GRAFIK TREN DUAL-MODE (HARIAN / MINGGUAN) & RECENT ACTIVITY -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ chartMode: 'harian' }">
            
            <!-- Dynamic Chart Card -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-4">
                    <h3 class="font-serif font-bold text-gray-800 flex items-center space-x-2">
                        <i class="fa-solid fa-chart-simple text-[#359FA0]"></i>
                        <span>Tren Pengeluaran Uang Makan</span>
                    </h3>

                    <!-- BUTTON SWITCHER [ HARIAN | MINGGUAN ] -->
                    <div class="flex bg-gray-100 p-1 rounded-xl text-xs font-semibold w-fit">
                        <button 
                            @click="chartMode = 'harian'; updateChart('harian')" 
                            :class="chartMode === 'harian' ? 'bg-[#359FA0] text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                            class="px-3 py-1.5 rounded-lg transition-all"
                        >
                            Harian (Minggu Ini)
                        </button>
                        <button 
                            @click="chartMode = 'mingguan'; updateChart('mingguan')" 
                            :class="chartMode === 'mingguan' ? 'bg-[#359FA0] text-white shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                            class="px-3 py-1.5 rounded-lg transition-all"
                        >
                            Mingguan (Bulan Ini)
                        </button>
                    </div>
                </div>

                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-serif font-bold text-gray-800 mb-4 flex items-center space-x-2">
                    <i class="fa-solid fa-clock-rotate-left text-[#359FA0]"></i>
                    <span>Transaksi Terakhir</span>
                </h3>
                <div class="space-y-3">
                    @forelse($recentActivities as $act)
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $act->tipe === 'pemasukan' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-600' }}">
                                    <i class="fa-solid {{ $act->tipe === 'pemasukan' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 truncate max-w-[110px]">{{ $act->keterangan }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $act->sumberDana ? $act->sumberDana->nama : 'Umum' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold {{ $act->tipe === 'pemasukan' ? 'text-emerald-600' : 'text-gray-800' }}">
                                    {{ $act->tipe === 'pemasukan' ? '+' : '-' }}Rp{{ number_format($act->jumlah, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($act->tanggal)->format('d/m') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-6">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- Script Chart.js Dynamic Switcher -->
    <script>
        let trendChartInstance = null;

        const dailyData = @json($dailyChart);
        const weeklyData = @json($weeklyChart);

        function renderChart(labels, dataValues, showBenchmark = false) {
            const ctx = document.getElementById('trendChart').getContext('2d');

            if (trendChartInstance) {
                trendChartInstance.destroy();
            }

            const datasets = [
                {
                    label: 'Pengeluaran (Rp)',
                    data: dataValues,
                    backgroundColor: '#359FA0',
                    borderRadius: 8,
                }
            ];

            if (showBenchmark) {
                datasets.push({
                    label: 'Benchmark (Rp40.000)',
                    data: Array(labels.length).fill(40000),
                    type: 'line',
                    borderColor: '#FF8C52',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false
                });
            }

            trendChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateChart(mode) {
            if (mode === 'harian') {
                renderChart(dailyData.labels, dailyData.data, true);
            } else {
                renderChart(weeklyData.labels, weeklyData.data, false);
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            updateChart('harian');
        });
    </script>
</x-app-layout>