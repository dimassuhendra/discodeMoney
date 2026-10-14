    <!-- PEMISAH HALAMAN (MASUK KE DATA MODUL PENGELUARAN) -->
    <div class="page-break"></div>

    <!-- ================= HALAMAN 4: DATA PENGELUARAN HARIAN & UANG MAKAN ================= -->
    <h3 style="font-size: 13px; color: #1e3e3f; margin-bottom: 10px;">2. RINGKASAN PENGELUARAN</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Indikator Pengeluaran</th>
                <th class="text-right">Total / Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f9fafb;">
                <td><strong>Total Pengeluaran Bulan Ini (Seluruh Sumber Dana)</strong></td>
                <td class="text-right font-bold text-red">
                    Rp {{ number_format($pengeluaran['total_pengeluaran'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Total Pengeluaran Uang Makan Bulan Ini</td>
                <td class="text-right font-bold">
                    Rp {{ number_format($pengeluaran['uang_makan_stats']['total'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Rata-Rata Uang Makan Harian (Benchmark: Rp40.000/hari)</td>
                <td class="text-right font-bold">
                    Rp {{ number_format($pengeluaran['uang_makan_stats']['avg_harian'] ?? 0, 0, ',', '.') }} / hari
                </td>
            </tr>
        </tbody>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Sorotan Pengeluaran Makan</th>
                <th class="text-center">Tanggal</th>
                <th class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pengeluaran Makan Terbesar (Max Day)</td>
                <td class="text-center">
                    {{ $pengeluaran['uang_makan_stats']['max_day']['tanggal'] ?? '-' }}
                </td>
                <td class="text-right font-bold text-red">
                    Rp {{ number_format($pengeluaran['uang_makan_stats']['max_day']['nominal'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td>Pengeluaran Makan Terkecil (Min Day)</td>
                <td class="text-center">
                    {{ $pengeluaran['uang_makan_stats']['min_day']['tanggal'] ?? '-' }}
                </td>
                <td class="text-right font-bold text-emerald">
                    Rp {{ number_format($pengeluaran['uang_makan_stats']['min_day']['nominal'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>