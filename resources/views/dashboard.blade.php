<x-app-layout>
    <x-slot name="header">
        Dashboard Utama
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6">

        <!-- BANNER DRAFT INVESTASI (Jika Ada Record Draft) -->
        @if($draftInvestasiCount > 0)
            <div class="bg-gradient-to-r from-[#FF8C52] to-[#e07740] rounded-2xl p-4 md:p-5 text-white shadow-lg flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-2xl">
                        💡
                    </div>
                    <div>
                        <h4 class="font-bold text-sm md:text-base">Draft Investasi Perlu Dilengkapi</h4>
                        <p class="text-xs text-white/90">Ada {{ $draftInvestasiCount }} transaksi pengeluaran investasi yang belum dilengkapi detail platform / harga unitnya.</p>
                    </div>
                </div>
                <a href="#" class="px-4 py-2 bg-white text-[#FF8C52] hover:bg-gray-100 font-semibold text-xs rounded-xl shadow-sm transition-all whitespace-nowrap">
                    Lengkapi Sekarang &rarr;
                </a>
            </div>
        @endif

        <!-- WIDGET 1: BUDGET UANG MAKAN (KEMARIN, HARI INI, BESOK) -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-serif font-bold text-lg text-gray-800 flex items-center gap-2">
                    <span>🍱</span> Budget Uang Makan
                </h2>
                <span class="text-xs text-gray-500">Benchmark: Rp40.000 / Hari</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Card 1: Kemarin -->
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                        <span>Kemarin ({{ $uangMakan['kemarin']['tanggal'] }})</span>
                        @if($uangMakan['kemarin']['is_overbudget'])
                            <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold text-[10px]">Overbudget</span>
                        @else
                            <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full font-bold text-[10px]">Aman</span>
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
                    <h3 class="font-serif font-bold text-gray-800 mb-4">Cashflow Bulan Ini</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">💰</span>
                                <div>
                                    <p class="text-xs text-emerald-700 font-semibold">Pemasukan</p>
                                    <p class="text-sm font-bold text-emerald-900">Rp{{ number_format($pemasukanBulanIni, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg">💸</span>
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

            <!-- Breakdown Sumber Dana -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-serif font-bold text-gray-800">Saldo Sumber Dana</h3>
                    <span class="text-xs font-semibold bg-[#8AD6D1]/20 text-[#359FA0] px-3 py-1 rounded-full">
                        Total Kas: Rp{{ number_format($totalSaldoAktif, 0, ',', '.') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($sumberDanaList as $sd)
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl">
                            <p class="text-xs text-gray-500 font-medium">{{ $sd->nama }}</p>
                            <p class="text-lg font-bold text-gray-800 mt-1">Rp{{ number_format($sd->saldo_aktif, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <!-- Stats Barang / Aset Bulan Ini -->
                <div class="mt-6 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400">Pembelian Barang Mati (Bulan Ini)</p>
                        <p class="text-sm font-bold text-gray-700">Rp{{ number_format($totalBarangMatiBulanIni, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Pembelian Barang Hidup/Aset (Bulan Ini)</p>
                        <p class="text-sm font-bold text-gray-700">Rp{{ number_format($totalBarangHidupBulanIni, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- WIDGET 3: GRAFIK TREN MINGGUAN & RECENT ACTIVITY -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Chart Pengeluaran Mingguan (2 Cols) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-serif font-bold text-gray-800">Tren Pengeluaran Uang Makan Minggu Ini</h3>
                    <span class="text-xs text-gray-400">Senin - Minggu</span>
                </div>
                <div class="h-64">
                    <canvas id="weeklyBudgetChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity (1 Col) -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-serif font-bold text-gray-800 mb-4">Transaksi Terakhir</h3>
                <div class="space-y-3">
                    @forelse($recentActivities as $act)
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-2.5">
                                <span class="text-base">{{ $act->tipe === 'pemasukan' ? '💰' : '💸' }}</span>
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

    <!-- Script Inisialisasi Chart.js Mingguan -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('weeklyBudgetChart').getContext('2d');
            
            const labels = @json($weeklyChart['labels']);
            const data = @json($weeklyChart['data']);
            const benchmark = {{ $weeklyChart['benchmark'] }};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pengeluaran Uang Makan (Rp)',
                            data: data,
                            backgroundColor: '#359FA0',
                            borderRadius: 8,
                        },
                        {
                            label: 'Benchmark (Rp40.000)',
                            data: Array(labels.length).fill(benchmark),
                            type: 'line',
                            borderColor: '#FF8C52',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false
                        }
                    ]
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
        });
    </script>
</x-app-layout>