<x-app-layout>
    <x-slot name="header">
        Ringkasan & Log Investasi
    </x-slot>

    <div class="space-y-6" x-data="{ 
        activeTab: 'holding',
        createModalOpen: false, 
        editModalOpen: false, 
        jualModalOpen: false,
        detailModalOpen: false,
        formStatus: 'masih_dimiliki',
        detailLoading: false,
        detailNamaAset: '',
        detailDetailAset: '',
        detailHistoryList: [],
        editData: { id: null, nama: '', jenis_investasi: 'Saham', platform: '', harga_beli: '', harga_jual: '', harga_beli_idr: '', harga_jual_idr: '', tanggal_beli: '', tanggal_jual: '', status: 'masih_dimiliki' },
        jualData: { id: null, nama: '', tanggal_jual: '{{ date('Y-m-d') }}', harga_jual: '', harga_jual_idr: '' },

        openDetailEmiten(namaAset) {
            this.detailNamaAset = namaAset;
            this.detailDetailAset = 'Memuat detail...';         
            this.detailModalOpen = true;
            this.detailLoading = true;
            fetch(`/investasi/history-by-nama?nama=${encodeURIComponent(namaAset)}`)
                .then(res => res.json())
                .then(data => {
                    this.detailHistoryList = data.history;
                    this.detailDetailAset = data.detail || 'Seluruh riwayat transaksi jual & beli aset ini';
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

        <!-- TOP CARDS ANALYSIS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-[#359FA0] to-[#1e3e3f] p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-[#8AD6D1] mb-2">
                    <span>Nilai Aset Aktif (Holding)</span>
                    <i class="fa-solid fa-chart-line text-[#FFF0C5]"></i>
                </div>
                <h3 class="text-2xl font-bold text-[#FFF0C5]">Rp{{ number_format($stats['total_holding'], 0, ',', '.') }}</h3>
                <p class="text-[10px] text-[#8AD6D1] mt-2">Modal Aset yang Masih Dimiliki</p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 mb-2">Porsi Aset Per Jenis</p>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500"><i class="fa-solid fa-chart-pie text-[#359FA0] text-[10px] mr-1"></i> Saham:</span>
                        <span class="font-bold text-gray-800">Rp{{ number_format($stats['breakdown_jenis']['Saham'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500"><i class="fa-solid fa-dollar-sign text-emerald-500 text-[10px] mr-1"></i> USD:</span>
                        <span class="font-bold text-gray-800">Rp{{ number_format($stats['breakdown_jenis']['USD'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500"><i class="fa-solid fa-yen-sign text-amber-500 text-[10px] mr-1"></i> JPY:</span>
                        <span class="font-bold text-gray-800">Rp{{ number_format($stats['breakdown_jenis']['JPY'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500"><i class="fa-solid fa-seedling text-purple-500 text-[10px] mr-1"></i> Reksadana:</span>
                        <span class="font-bold text-gray-800">Rp{{ number_format($stats['breakdown_jenis']['Reksadana'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                    <span>Durasi Hold Terlama (Aktif)</span>
                    <i class="fa-solid fa-hourglass-half text-amber-500"></i>
                </div>
                @if($stats['longest_holding_asset'])
                    <div>
                        <h4 class="font-bold text-gray-800 text-base truncate hover:text-[#359FA0] cursor-pointer" @click="openDetailEmiten('{{ $stats['longest_holding_asset']->nama }}')">
                            {{ $stats['longest_holding_asset']->nama }}
                        </h4>
                        <p class="text-lg font-bold text-[#359FA0] mt-0.5">{{ $stats['longest_holding_asset']->holding_days }} Hari</p>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Beli: {{ \Carbon\Carbon::parse($stats['longest_holding_asset']->tanggal_beli)->format('d/m/Y') }}</p>
                @else
                    <p class="text-xs text-gray-400">Belum ada aset aktif</p>
                @endif
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                    <span>Durasi Sold Tercepat</span>
                    <i class="fa-solid fa-bolt text-[#FF8C52]"></i>
                </div>
                @if($stats['fastest_sold_asset'])
                    <div>
                        <h4 class="font-bold text-gray-800 text-base truncate hover:text-[#FF8C52] cursor-pointer" @click="openDetailEmiten('{{ $stats['fastest_sold_asset']->nama }}')">
                            {{ $stats['fastest_sold_asset']->nama }}
                        </h4>
                        <p class="text-lg font-bold text-[#FF8C52] mt-0.5">{{ $stats['fastest_sold_asset']->holding_days }} Hari</p>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Status: Selesai Dijual</p>
                @else
                    <p class="text-xs text-gray-400">Belum ada transaksi sold</p>
                @endif
            </div>
        </div>

        <!-- FILTER TOOLBAR & ACTION BUTTON -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
            <form action="{{ route('investasi.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative flex-1 min-w-[160px]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aset (BBCA, USD...)" class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                </div>

                <div class="min-w-[130px]">
                    <select name="jenis_investasi" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:ring-2 focus:ring-[#359FA0] outline-none">
                        <option value="">Semua Jenis</option>
                        <option value="Saham" {{ request('jenis_investasi') == 'Saham' ? 'selected' : '' }}>Saham</option>
                        <option value="USD" {{ request('jenis_investasi') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="JPY" {{ request('jenis_investasi') == 'JPY' ? 'selected' : '' }}>JPY</option>
                        <option value="Reksadana" {{ request('jenis_investasi') == 'Reksadana' ? 'selected' : '' }}>Reksadana</option>
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-[#359FA0] text-white font-semibold text-xs rounded-xl transition-all flex items-center space-x-1">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>

                @if(request()->hasAny(['search', 'jenis_investasi']))
                    <a href="{{ route('investasi.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 font-semibold text-xs rounded-xl transition-all">Reset</a>
                @endif
            </form>

            <button 
                @click="createModalOpen = true"
                class="px-5 py-2.5 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold text-xs rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 whitespace-nowrap"
            >
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Catatan Investasi</span>
            </button>
        </div>

        <!-- TAB NAVIGATION (HOLDING VS SOLD) -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex border-b border-gray-100 bg-gray-50/50 p-1.5">
                <button 
                    @click="activeTab = 'holding'" 
                    :class="activeTab === 'holding' ? 'bg-white text-[#359FA0] shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 font-medium'"
                    class="flex-1 py-3 px-4 text-xs rounded-xl transition-all flex items-center justify-center space-x-2"
                >
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Portofolio Aktif (Holding)</span>
                    <span class="bg-[#359FA0] text-white text-[10px] px-2 py-0.5 rounded-full font-bold ml-1">{{ $holdingList->count() }}</span>
                </button>

                <button 
                    @click="activeTab = 'sold'" 
                    :class="activeTab === 'sold' ? 'bg-white text-[#359FA0] shadow-sm font-bold' : 'text-gray-500 hover:text-gray-800 font-medium'"
                    class="flex-1 py-3 px-4 text-xs rounded-xl transition-all flex items-center justify-center space-x-2"
                >
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Histori Selesai (Sold)</span>
                    <span class="bg-gray-200 text-gray-700 text-[10px] px-2 py-0.5 rounded-full font-bold ml-1">{{ $soldList->count() }}</span>
                </button>
            </div>

            <!-- TAB 1: PORTOFOLIO AKTIF (HOLDING) -->
            <div x-show="activeTab === 'holding'" class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-4">Nama Aset</th>
                            <th class="py-3.5 px-4">Jenis & Platform</th>
                            <th class="py-3.5 px-4">Harga Beli Unit</th>
                            <th class="py-3.5 px-4">Total Real Beli (IDR)</th>
                            <th class="py-3.5 px-4">Tgl Beli (Durasi)</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($holdingList as $h)
                            @php $daysHeld = \Carbon\Carbon::parse($h->tanggal_beli)->diffInDays(now()); @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <!-- Tekan Nama Aset untuk Buka Detail Histori Emiten -->
                                <td class="py-3.5 px-4 font-bold text-[#359FA0] hover:underline cursor-pointer" @click="openDetailEmiten('{{ $h->nama }}')">
                                    <i class="fa-solid fa-circle-info text-[11px] mr-1 text-[#8AD6D1]"></i>
                                    {{ $h->nama }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="bg-[#8AD6D1]/20 text-[#359FA0] font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                        {{ $h->jenis_investasi }}
                                    </span>
                                    <span class="text-gray-400 text-[11px] ml-1">{{ $h->platform ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-gray-700 whitespace-nowrap">
                                    {{ $h->harga_beli ? number_format($h->harga_beli, 2) : '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap">
                                    {{ $h->harga_beli_idr ? 'Rp' . number_format($h->harga_beli_idr, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($h->tanggal_beli)->format('d/m/Y') }}</p>
                                    <p class="text-[10px] text-amber-600 font-semibold"><i class="fa-regular fa-clock"></i> {{ $daysHeld }} Hari Hold</p>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button 
                                            @click="
                                                jualData.id = '{{ $h->id }}';
                                                jualData.nama = '{{ $h->nama }}';
                                                jualData.harga_jual = '{{ $h->harga_jual }}';
                                                jualData.harga_jual_idr = '';
                                                jualModalOpen = true;
                                            "
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[11px] transition-all flex items-center space-x-1 shadow-sm"
                                        >
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                            <span>Jual</span>
                                        </button>

                                        <button 
                                            @click="
                                                editData.id = '{{ $h->id }}';
                                                editData.nama = '{{ $h->nama }}';
                                                editData.jenis_investasi = '{{ $h->jenis_investasi }}';
                                                editData.platform = '{{ $h->platform }}';
                                                editData.harga_beli = '{{ $h->harga_beli }}';
                                                editData.harga_jual = '{{ $h->harga_jual }}';
                                                editData.harga_beli_idr = '{{ $h->harga_beli_idr }}';
                                                editData.harga_jual_idr = '{{ $h->harga_jual_idr }}';
                                                editData.tanggal_beli = '{{ $h->tanggal_beli->format('Y-m-d') }}';
                                                editData.tanggal_jual = '{{ $h->tanggal_jual ? $h->tanggal_jual->format('Y-m-d') : '' }}';
                                                editData.status = '{{ $h->status }}';
                                                editModalOpen = true;
                                            "
                                            class="text-gray-400 hover:text-[#359FA0] p-1.5 transition-colors"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form action="{{ route('investasi.destroy', $h->id) }}" method="POST" onsubmit="return confirm('Hapus catatan aset ini?');">
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
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-chart-line text-3xl mb-2 text-gray-300"></i>
                                    <p>Tidak ada portofolio aset aktif saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- TAB 2: HISTORI SELESAI (SOLD) -->
            <div x-show="activeTab === 'sold'" class="overflow-x-auto" style="display: none;">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider border-b border-gray-100">
                        <tr>
                            <th class="py-3.5 px-4">Nama Aset</th>
                            <th class="py-3.5 px-4">Jenis & Platform</th>
                            <th class="py-3.5 px-4">Total Beli (IDR)</th>
                            <th class="py-3.5 px-4">Total Jual (IDR)</th>
                            <th class="py-3.5 px-4">Gain / Loss (ROI)</th>
                            <th class="py-3.5 px-4">Durasi Simpan</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($soldList as $s)
                            @php
                                $gainLoss = $s->gain_loss_idr;
                                $roi = $s->roi_percentage;
                                $tglBeli = \Carbon\Carbon::parse($s->tanggal_beli);
                                $tglJual = $s->tanggal_jual ? \Carbon\Carbon::parse($s->tanggal_jual) : $tglBeli;
                                $durasiDays = $tglBeli->diffInDays($tglJual);
                            @endphp
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <!-- Tekan Nama Aset untuk Buka Detail Histori Emiten -->
                                <td class="py-3.5 px-4 font-bold text-[#359FA0] hover:underline cursor-pointer" @click="openDetailEmiten('{{ $s->nama }}')">
                                    <i class="fa-solid fa-circle-info text-[11px] mr-1 text-[#8AD6D1]"></i>
                                    {{ $s->nama }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="bg-gray-100 text-gray-700 font-bold px-2.5 py-0.5 rounded-full text-[10px]">
                                        {{ $s->jenis_investasi }}
                                    </span>
                                    <span class="text-gray-400 text-[11px] ml-1">{{ $s->platform ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-gray-700 whitespace-nowrap">
                                    {{ $s->harga_beli_idr ? 'Rp' . number_format($s->harga_beli_idr, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-900 whitespace-nowrap">
                                    {{ $s->harga_jual_idr ? 'Rp' . number_format($s->harga_jual_idr, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold whitespace-nowrap">
                                    @if($gainLoss !== null &&$gainLoss > 0)
                                        <span class="text-emerald-600">+Rp{{ number_format($gainLoss, 0, ',', '.') }} (+{{ number_format($roi, 1) }}%)</span>
                                    @elseif($gainLoss !== null &&$gainLoss < 0)
                                        <span class="text-red-500">-Rp{{ number_format(abs($gainLoss), 0, ',', '.') }} ({{ number_format($roi, 1) }}%)</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <p class="font-medium text-gray-800">{{ max(1, $durasiDays) }} Hari</p>
                                    <p class="text-[10px] text-gray-400">{{ $tglBeli->format('d/m/y') }} - {{$tglJual->format('d/m/y') }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <form action="{{ route('investasi.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus histori investasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 p-1.5 transition-colors">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    <i class="fa-solid fa-circle-check text-3xl mb-2 text-gray-300"></i>
                                    <p>Belum ada histori penjualan aset.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('module.investasi._modal-form')

    </div>
</x-app-layout>