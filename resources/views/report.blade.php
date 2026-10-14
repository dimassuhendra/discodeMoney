<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</title>
    <style>
        @page {
            margin: 1.2cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.5;
        }

        /* HEADER STYLING */
        .report-header {
            border-bottom: 2px solid #359FA0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
            border: none;
        }

        .system-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3e3f;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #359FA0;
            margin-top: 2px;
        }

        .meta-info {
            text-align: right;
            font-size: 9px;
            color: #6b7280;
            line-height: 1.4;
        }

        /* SECTION & GUIDE STYLING (PAGE 1) */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3e3f;
            margin-top: 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #359FA0;
            padding-bottom: 4px;
        }

        .guide-box {
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #e5e7eb;
        }

        .guide-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e3e3f;
        }

        .guide-desc {
            font-size: 10px;
            color: #4b5563;
        }

        /* TABLE DATA STYLING (PAGE 2, 3, DST) */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            text-align: left;
        }

        table.data-table th {
            background-color: #f9fafb;
            color: #374151;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-emerald { color: #059669; }
        .text-red { color: #dc2626; }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- ================= MASTER HEADER LAPORAN ================= -->
    <div class="report-header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="system-title">LAPORAN KEUANGAN BULAN {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
                </td>
                <td class="meta-info">
                    <div><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= HALAMAN 1: COPYWRITING & PANDUAN RINGKAS SEMENTARA ================= -->
    <h3 style="font-size: 14px; color: #1e3e3f; text-align: center; margin-bottom: 16px;">PANDUAN LENGKAP INDIKATOR KEUANGAN & ASET</h3>

    <!-- 1. Modul Dashboard -->
    <div class="section-title">A. Ringkasan Dashboard & Arus Kas</div>
    <div class="guide-box">
        <div class="guide-title">1. Cashflow Bulan Ini</div>
        <div class="guide-desc">Menampilkan perbandingan total uang masuk dan keluar pada bulan berjalan untuk menghitung sisa bersih (Net Cashflow).</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">2. Saldo Sumber Dana</div>
        <div class="guide-desc">Rincian sisa saldo aktif yang tersimpan pada masing-masing rekening/dompet keuangan Anda.</div>
    </div>

    <!-- 2. Modul Pengeluaran -->
    <div class="section-title">B. Pengeluaran Harian & Uang Makan</div>
    <div class="guide-box">
        <div class="guide-title">1. Total Pengeluaran Bulan Ini</div>
        <div class="guide-desc">Jumlah akumulasi seluruh transaksi uang keluar dari semua sumber dana pada bulan berjalan.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">2. Evaluasi Uang Makan Harian</div>
        <div class="guide-desc">Rata-rata pengeluaran konsumsi harian dibandingkan dengan batas acuan (benchmark) Rp40.000/hari.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">3. Sorotan Fluktuasi Makan</div>
        <div class="guide-desc">Mencatat tanggal dan nominal saat pengeluaran makan mencapai titik tertinggi dan terendah dalam sebulan.</div>
    </div>

    <!-- 3. Modul Pemasukan -->
    <div class="section-title">C. Pemasukan & Pendapatan</div>
    <div class="guide-box">
        <div class="guide-title">1. Pemasukan Bulan Ini</div>
        <div class="guide-desc">Total dana atau arus kas masuk yang diterima seluruhnya pada bulan berjalan.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">2. Target Rekening Dominan (Penerima Terbesar)</div>
        <div class="guide-desc">Rekening atau wallet yang paling banyak menerima alokasi pencairan/pemasukan dana.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">3. Pemasukan Tahun Ini (Year-to-Date / YTD)</div>
        <div class="guide-desc">Akumulasi total seluruh pendapatan dan uang masuk yang terkumpul sejak awal tahun hingga hari ini.</div>
    </div>

    <!-- 4. Modul Investasi -->
    <div class="section-title">D. Portofolio & Log Investasi</div>
    <div class="guide-box">
        <div class="guide-title">1. Total Nilai Aset Aktif (Holding)</div>
        <div class="guide-desc">Total modal investasi yang saat ini masih ditanamkan dan aktif dimiliki pada berbagai instrumen instrumen.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">2. Distribusi Instrumen Investasi</div>
        <div class="guide-desc">Porsi pembagian modal ke dalam instrumen Saham, Valas (USD, JPY), dan Reksadana.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">3. Analisis Durasi Hold Aset</div>
        <div class="guide-desc">Melihat daya simpan aset aktif terlama serta kecepatan penjualan aset yang sudah selesai ditransaksikan.</div>
    </div>

    <!-- 5. Modul Inventaris Barang -->
    <div class="section-title">E. Inventaris Barang & Aset</div>
    <div class="guide-box">
        <div class="guide-title">1. Total Nilai Inventaris</div>
        <div class="guide-desc">Akumulasi estimasi nilai seluruh barang fisik (Barang Operasional + Barang Produktif) yang Anda miliki saat ini.</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">2. Barang Operasional vs Produktif</div>
        <div class="guide-desc">Membedakan alokasi biaya antara alat pendukung harian (operasional) dan barang investasi utama pembawa hasil (produktif).</div>
    </div>
    <div class="guide-box">
        <div class="guide-title">3. Kebutuhan Rutin</div>
        <div class="guide-desc">Total belanja untuk barang-barang konsumtif yang sifatnya habis pakai (seperti sembako dan perlengkapan harian).</div>
    </div>

    
    @include('module.pdf._utama')
    @include('module.pdf._pengeluaran')
    @include('module.pdf._pemasukan')
    @include('module.pdf._investasi')
    @include('module.pdf._barang')

</body>
</html>