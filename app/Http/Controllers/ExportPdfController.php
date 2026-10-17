<?php

namespace App\Http\Controllers;

use App\Services\SumberDanaService;
use App\Services\PengeluaranService;
use App\Services\PemasukanService;
use App\Services\InvestasiService;
use App\Services\BarangService;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportPdfController extends Controller
{
    protected $sumberDanaService;
    protected $pengeluaranService;
    protected $pemasukanService;
    protected $investasiService;
    protected $barangService;

    public function __construct(
        SumberDanaService $sumberDanaService,
        PengeluaranService $pengeluaranService,
        PemasukanService $pemasukanService,
        InvestasiService $investasiService,
        BarangService $barangService
    ) {
        $this->sumberDanaService = $sumberDanaService;
        $this->pengeluaranService = $pengeluaranService;
        $this->pemasukanService = $pemasukanService;
        $this->investasiService = $investasiService;
        $this->barangService = $barangService;

    }

    /**
     * Export Laporan Konsolidasi Seluruh Modul ke Satu File PDF
     */
    public function export(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth   = Carbon::now()->endOfMonth()->format('Y-m-d');

        // 1. DATA MODUL DASHBOARD & SALDO DANA
        $sumberDanaList = $this->sumberDanaService->getSumberDanaSummary($userId);
        $totalSaldoAktif = $sumberDanaList->sum('saldo_aktif');

        $pemasukanBulanIni = Pemasukan::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $pengeluaranBulanIni = Pengeluaran::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $dashboardData = [
            'sumberDanaList'      => $sumberDanaList,
            'totalSaldoAktif'     => $totalSaldoAktif,
            'pemasukanBulanIni'   => $pemasukanBulanIni,
            'pengeluaranBulanIni' => $pengeluaranBulanIni,
            'netCashflow'         => $pemasukanBulanIni - $pengeluaranBulanIni,
        ];

        $pengeluaranStats = $this->pengeluaranService->getPengeluaranPageData($userId);
        $pemasukanStats = $this->pemasukanService->getPemasukanStats($userId, $startDate, $endDate);
        $investasiStats = $this->investasiService->getInvestasiStats($userId);
        $barangStats = $this->barangService->getBarangStats($userId, $startDate, $endDate);

        // 4. BIND SEMUA DATA KE SATU VIEW PDF UTUH
        $pdf = Pdf::loadView('report', [
            'dashboard' => $dashboardData,
            'pengeluaran' => $pengeluaranStats,
            'pemasukan' => $pemasukanStats,
            'investasi' => $investasiStats,
            'barang'    => $barangStats,
        ])
        ->setOption(
            'isRemoteEnabled',
            true)
        ->setOption('isHtml5ParserEnabled', true)
        ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan.pdf');
    }
}
