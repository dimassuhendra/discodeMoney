    <!-- ================= HALAMAN 3: DATA PEMASUKAN & PENDAPATAN ================= -->
    <h3 style="font-size: 13px; color: #1e3e3f; margin-bottom: 10px;">3. RINGKASAN PEMASUKAN</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Indikator Pemasukan</th>
                <th class="text-right">Total Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f9fafb;">
                <td><strong>Pemasukan Bulan Ini</strong></td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($pemasukan['bulan_ini'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Total Pemasukan (Filter / Transaksi)</td>
                <td class="text-right font-bold">
                    Rp {{ number_format($pemasukan['total_filtered'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Pemasukan Tahun Ini (Year-to-Date / YTD)</td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($pemasukan['pemasukan_ytd'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 15px; margin-bottom: 15px;">
        <h4 style="font-size: 11px; color: #1e3e3f; margin-bottom: 5px;">Grafik Trend Pemasukan Bulanan</h4>
        <img src="https://quickchart.io/chart?c={{ urlencode(json_encode([
            'type' => 'bar',
            'data' => [
                'labels' => $pemasukan['chart_bar']['labels'] ?? [],
                'datasets' => [[
                    'label' => 'Total Pemasukan (Rp)',
                    'data' => $pemasukan['chart_bar']['values'] ?? [],
                    'backgroundColor' => '#359fa0'
                ]]
            ],
            'options' => [
                'plugins' => [
                    'legend' => ['display' => false]
                ]
            ]
        ])) }}" style="width: 100%; max-width: 480px; height: auto;">
    </div>

    {{-- Ditutup Sementara --}}
    {{-- <table class="data-table">
        <thead>
            <tr>
                <th>Sorotan Alokasi Rekening</th>
                <th class="text-right">Detail Wallet</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rekening / Wallet Penerima Terbesar</td>
                <td class="text-right font-bold">
                    {{ $pemasukan['top_sumber_dana'] ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table> --}}
    {{-- ------------------ --}}