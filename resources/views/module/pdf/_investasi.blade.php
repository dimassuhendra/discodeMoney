    <!-- PEMISAH HALAMAN (MASUK KE DATA MODUL INVESTASI) -->
    <div class="page-break"></div>

    <!-- ================= HALAMAN 6: DATA PORTOFOLIO INVESTASI ================= -->
    <h3 style="font-size: 13px; color: #1e3e3f; margin-bottom: 10px;">5. RINGKASAN PORTOFOLIO INVESTASI</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Indikator Portofolio</th>
                <th class="text-right">Nilai Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f9fafb;">
                <td><strong>Nilai Aset Aktif Saat Ini (Holding)</strong></td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($investasi['total_holding'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- CHART DONUT PORSI INSTRUMEN INVESTASI (QUICKCHART.IO) -->
    <div style="text-align: center; margin-top: 15px; margin-bottom: 15px;">
        <h4 style="font-size: 11px; color: #1e3e3f; margin-bottom: 6px;">Porsi Aset Per Jenis Investasi</h4>
        @php
            $breakdown = $investasi['breakdown_jenis'] ?? [];
            $chartInvestasiConfig = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => array_keys($breakdown),
                    'datasets' => [[
                        'data' => array_values($breakdown),
                        'backgroundColor' => ['#359FA0', '#10B981', '#F59E0B', '#8B5CF6']
                    ]]
                ],
                'options' => [
                    'plugins' => [
                        'legend' => ['position' => 'bottom']
                    ]
                ]
            ];
            $chartInvestasiUrl = 'https://quickchart.io/chart?w=350&h=200&c=' . urlencode(json_encode($chartInvestasiConfig));
        @endphp
        <img src="{{ $chartInvestasiUrl }}" style="width: 100%; max-width: 280px; height: auto;">
    </div>

    <!-- TABEL BREAKDOWN PER JENIS -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Jenis Instrumen Investasi</th>
                <th class="text-right">Nominal Terikat (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($investasi['breakdown_jenis'] ?? [] as $jenis => $nominal)
                <tr>
                    <td>{{ $jenis }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- HIGHLIGHT DURASI HOLDING -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Sorotan Durasi Simpan</th>
                <th class="text-center">Nama Aset</th>
                <th class="text-right">Durasi Hold</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Aset Aktif Hold Terlama</td>
                <td class="text-center font-bold">
                    {{ $investasi['longest_holding_asset']->nama ?? '-' }}
                </td>
                <td class="text-right font-bold">
                    {{ $investasi['longest_holding_asset']->holding_days ?? 0 }} Hari
                </td>
            </tr>
            <tr>
                <td>Aset Dijual (Sold) Tercepat</td>
                <td class="text-center font-bold">
                    {{ $investasi['fastest_sold_asset']->nama ?? '-' }}
                </td>
                <td class="text-right font-bold text-red">
                    {{ $investasi['fastest_sold_asset']->holding_days ?? 0 }} Hari
                </td>
            </tr>
        </tbody>
    </table>