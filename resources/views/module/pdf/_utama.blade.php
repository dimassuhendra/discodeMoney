    <!-- PEMISAH HALAMAN (MASUK KE DATA MODUL DASHBOARD) -->
    <div class="page-break"></div>

    <!-- ================= HALAMAN 2: DATA RINGKASAN DASHBOARD ================= -->
    <h3 style="font-size: 13px; color: #1e3e3f; margin-bottom: 10px;">1. RINGKASAN CASHFLOW & SALDO KAS</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th colspan="2">Arus Kas Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Pemasukan Bulan Ini</td>
                <td class="text-right font-bold text-emerald">Rp {{ number_format($dashboard['pemasukanBulanIni'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran Bulan Ini</td>
                <td class="text-right font-bold text-red">Rp {{ number_format($dashboard['pengeluaranBulanIni'] ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #f9fafb;">
                <td><strong>Net Cashflow Bersih</strong></td>
                <td class="text-right font-bold {{ ($dashboard['netCashflow'] ?? 0) >= 0 ? 'text-emerald' : 'text-red' }}">
                    {{ ($dashboard['netCashflow'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($dashboard['netCashflow'] ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Sumber Dana</th>
                <th class="text-right">Sisa Saldo Aktif</th>
                <th class="text-center">Sisa Kuota (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dashboard['sumberDanaList'] as $sd)
                <tr>
                    <td><strong>{{ $sd->nama }}</strong></td>
                    <td class="text-right font-bold">Rp {{ number_format($sd->saldo_aktif, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $sd->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada sumber dana.</td>
                </tr>
            @endforelse
            <tr style="background-color: #f9fafb;">
                <td><strong>TOTAL SALDO KAS AKTIF</strong></td>
                <td class="text-right font-bold text-emerald">Rp {{ number_format($dashboard['totalSaldoAktif'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tbody>
    </table>