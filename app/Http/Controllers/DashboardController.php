<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\PencatatanInvestasi;
use App\Models\SumberDana;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index()
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Budget Uang Makan Summary (Kemarin, Hari Ini, Besok)
        $uangMakan = $this->budgetService->getUangMakanSummary($userId);

        // 2. Saldo Per Sumber Dana
        $sumberDanaList = SumberDana::where('user_id', $userId)->get()->map(function ($sd) {
            $totalIn = Pemasukan::where('sumber_dana_id', $sd->id)->sum('jumlah');
            $totalOut = Pengeluaran::where('sumber_dana_id', $sd->id)->sum('jumlah');
            $sd->saldo_aktif = ($sd->budget ?? 0) + $totalIn - $totalOut;

            return $sd;
        });

        $totalSaldoAktif = $sumberDanaList->sum('saldo_aktif');

        // 3. Performa Bulan Ini
        $pemasukanBulanIni = Pemasukan::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $pengeluaranBulanIni = Pengeluaran::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('jumlah');

        $netCashflow = $pemasukanBulanIni - $pengeluaranBulanIni;

        // 4. Draft Investasi Perlu Dilengkapi
        $draftInvestasiCount = PencatatanInvestasi::where('user_id', $userId)
            ->whereNull('jenis_investasi')
            ->count();

        // 5. Quick Stats Aset & Barang Bulan Ini
        $totalBarangMatiBulanIni = Barang::where('user_id', $userId)
            ->where('jenis_barang', 'barang_mati')
            ->whereBetween('tanggal_beli', [$startOfMonth, $endOfMonth])
            ->sum('harga');

        $totalBarangHidupBulanIni = Barang::where('user_id', $userId)
            ->where('jenis_barang', 'barang_hidup')
            ->whereBetween('tanggal_beli', [$startOfMonth, $endOfMonth])
            ->sum('harga');

        // 6. Recent Activity (10 Transaksi Terakhir)
        $recentPengeluaran = Pengeluaran::with('sumberDana')
            ->where('user_id', $userId)
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'sumber_dana_id', DB::raw("'pengeluaran' as tipe"));

        $recentPemasukan = Pemasukan::with('sumberDana')
            ->where('user_id', $userId)
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'sumber_dana_id', DB::raw("'pemasukan' as tipe"));

        $recentActivities = $recentPengeluaran->union($recentPemasukan)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        // 7. Data Chart Mingguan
        $weeklyChart = $this->budgetService->getWeeklyChartData($userId);

        return view('dashboard', compact(
            'uangMakan',
            'sumberDanaList',
            'totalSaldoAktif',
            'pemasukanBulanIni',
            'pengeluaranBulanIni',
            'netCashflow',
            'draftInvestasiCount',
            'totalBarangMatiBulanIni',
            'totalBarangHidupBulanIni',
            'recentActivities',
            'weeklyChart'
        ));
    }
}
