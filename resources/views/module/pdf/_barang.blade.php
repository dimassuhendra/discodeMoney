<!-- PEMISAH HALAMAN (MASUK KE DATA MODUL INVENTARIS BARANG) -->
    <div class="page-break"></div>

    <!-- ================= HALAMAN: DATA INVENTARIS BARANG & ASET ================= -->
    <h3 style="font-size: 13px; color: #1e3e3f; margin-bottom: 10px;">2. RINGKASAN INVENTARIS BARANG & KEPEMILIKAN ASET</h3>

    <!-- TABEL RINGKASAN AGREGASI KATEGORI -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Kategori Inventaris</th>
                <th class="text-center">Jumlah Item</th>
                <th class="text-right">Total Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f9fafb;">
                <td><strong>Total Nilai Inventaris (Aset Aktif)</strong></td>
                <td class="text-center font-bold">{{ $barang['total_count'] ?? 0 }} Item</td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($barang['total_nilai'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Barang Operasional</td>
                <td class="text-center">{{ $barang['operasional']['count'] ?? 0 }} Item</td>
                <td class="text-right font-bold">
                    Rp {{ number_format($barang['operasional']['total'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Barang Produktif</td>
                <td class="text-center">{{ $barang['produktif']['count'] ?? 0 }} Item</td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($barang['produktif']['total'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Kebutuhan Rutin / Sembako</td>
                <td class="text-center">{{ $barang['rutin']['count'] ?? 0 }} Item</td>
                <td class="text-right font-bold">
                    Rp {{ number_format($barang['rutin']['total'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- DUAL DONUT CHARTS (ASET ALL TIME VS PEMBELIAN MTD) -->
    <table style="width: 100%; margin-top: 15px; margin-bottom: 15px; border: none;">
        <tr>
            <!-- Donut 1: Persentase Aset Dimiliki (All Time) -->
            <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                <h4 style="font-size: 10px; color: #1e3e3f; margin-bottom: 6px;">Persentase Aset Dimiliki (All-Time)</h4>
                @php
                    $chartPieAsetConfig = [
                        'type' => 'doughnut',
                        'data' => [
                            'labels' => ['Operasional', 'Produktif'],
                            'datasets' => [[
                                'data' => [
                                    $barang['chart_pie_aset']['barang_operasional'] ?? 0,
                                    $barang['chart_pie_aset']['barang_produktif'] ?? 0
                                ],
                                'backgroundColor' => ['#1e3e3f', '#359FA0']
                            ]]
                        ],
                        'options' => [
                            'plugins' => [
                                'legend' => ['position' => 'bottom']
                            ]
                        ]
                    ];
                    $chartPieAsetUrl = 'https://quickchart.io/chart?w=300&h=180&c=' . urlencode(json_encode($chartPieAsetConfig));
                @endphp
                <img src="{{ $chartPieAsetUrl }}" style="width: 100%; max-width: 220px; height: auto;">
            </td>

            <!-- Donut 2: Pembelian Bulan Ini (MTD) -->
            <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                <h4 style="font-size: 10px; color: #1e3e3f; margin-bottom: 6px;">Pembelian Bulan Ini (MTD)</h4>
                @php
                    $chartPieMtdConfig = [
                        'type' => 'doughnut',
                        'data' => [
                            'labels' => ['Operasional', 'Produktif', 'Rutin'],
                            'datasets' => [[
                                'data' => [
                                    $barang['chart_pie_mtd']['barang_operasional'] ?? 0,
                                    $barang['chart_pie_mtd']['barang_produktif'] ?? 0,
                                    $barang['chart_pie_mtd']['kebutuhan_rutin'] ?? 0
                                ],
                                'backgroundColor' => ['#1e3e3f', '#359FA0', '#FF8C52']
                            ]]
                        ],
                        'options' => [
                            'plugins' => [
                                'legend' => ['position' => 'bottom']
                            ]
                        ]
                    ];
                    $chartPieMtdUrl = 'https://quickchart.io/chart?w=300&h=180&c=' . urlencode(json_encode($chartPieMtdConfig));
                @endphp
                <img src="{{ $chartPieMtdUrl }}" style="width: 100%; max-width: 220px; height: auto;">
            </td>
        </tr>
    </table>

    <!-- GROUPED BAR CHART: TREND PEMBELIAN BULANAN -->
    <div style="text-align: center; margin-top: 15px; margin-bottom: 15px;">
        <h4 style="font-size: 11px; color: #1e3e3f; margin-bottom: 6px;">Trend Pembelian Bulanan Per Kategori</h4>
        @php
            $chartTrendMonthlyConfig = [
                'type' => 'bar',
                'data' => [
                    'labels' => $barang['growth_monthly']['labels'] ?? [],
                    'datasets' => [
                        [
                            'label' => 'Operasional',
                            'data' => $barang['growth_monthly']['barang_operasional'] ?? [],
                            'backgroundColor' => '#1e3e3f'
                        ],
                        [
                            'label' => 'Produktif',
                            'data' => $barang['growth_monthly']['barang_produktif'] ?? [],
                            'backgroundColor' => '#359FA0'
                        ],
                        [
                            'label' => 'Rutin',
                            'data' => $barang['growth_monthly']['kebutuhan_rutin'] ?? [],
                            'backgroundColor' => '#FF8C52'
                        ]
                    ]
                ],
                'options' => [
                    'plugins' => [
                        'legend' => ['position' => 'bottom']
                    ],
                    'scales' => [
                        'x' => ['stacked' => true],
                        'y' => ['stacked' => true]
                    ]
                ]
            ];
            $chartTrendMonthlyUrl = 'https://quickchart.io/chart?w=500&h=200&c=' . urlencode(json_encode($chartTrendMonthlyConfig));
        @endphp
        <img src="{{ $chartTrendMonthlyUrl }}" style="width: 100%; max-width: 460px; height: auto;">
    </div>

    <!-- HIGHLIGHT METRICS (AVG & MAX ITEM) -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Indikator Analisis / Highlight Item</th>
                <th class="text-center">Nama Item</th>
                <th class="text-right">Nilai / Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rata-Rata Harga Barang Operasional</td>
                <td class="text-center">-</td>
                <td class="text-right font-bold">
                    Rp {{ number_format($barang['avg_barang_operasional'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Rata-Rata Harga Barang Produktif</td>
                <td class="text-center">-</td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($barang['avg_barang_produktif'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Barang Operasional Terbesar</td>
                <td class="text-center font-bold">
                    {{ $barang['max_barang_operasional']->nama_barang ?? '-' }}
                </td>
                <td class="text-right font-bold">
                    Rp {{ number_format($barang['max_barang_operasional']->harga ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Barang Produktif Terbesar</td>
                <td class="text-center font-bold">
                    {{ $barang['max_barang_produktif']->nama_barang ?? '-' }}
                </td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($barang['max_barang_produktif']->harga ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>