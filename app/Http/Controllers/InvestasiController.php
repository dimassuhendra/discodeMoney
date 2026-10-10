<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvestasiRequest;
use App\Models\PencatatanInvestasi;
use App\Services\InvestasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestasiController extends Controller
{
    protected $investasiService;

    public function __construct(InvestasiService $investasiService)
    {
        $this->investasiService = $investasiService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $stats = $this->investasiService->getInvestasiStats($userId);

        $holdingQuery = PencatatanInvestasi::where('user_id', $userId)
            ->where('status', 'masih_dimiliki');

        $soldQuery = PencatatanInvestasi::where('user_id', $userId)
            ->where('status', 'sudah_dijual');

        if ($request->filled('search')) {
            $holdingQuery->where('nama', 'like', '%' . $request->search . '%');
            $soldQuery->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis_investasi')) {
            $holdingQuery->where('jenis_investasi', $request->jenis_investasi);
            $soldQuery->where('jenis_investasi', $request->jenis_investasi);
        }

        $holdingList = $holdingQuery->orderBy('tanggal_beli', 'desc')->get();
        $soldList = $soldQuery->orderBy('tanggal_jual', 'desc')->orderBy('tanggal_beli', 'desc')->get();

        // Seluruh transaksi untuk pencarian histori emiten/aset di Alpine.js Modal
        $allInvestasi = PencatatanInvestasi::where('user_id', $userId)
            ->orderBy('tanggal_beli', 'desc')
            ->get();

        return view('module.investasi.index', compact('stats', 'holdingList', 'soldList', 'allInvestasi'));
    }

    /**
     * Endpoint API JSON untuk mengambil histori lengkap berdasarkan Nama Aset / Emiten
     */
    public function getHistoryByNama(Request $request)
    {
        $namaAset = $request->input('nama');
        $detailAset = PencatatanInvestasi::where('user_id', Auth::id())
            ->where('nama', $namaAset)
            ->value('detail');
        $userId = Auth::id();

        $history = PencatatanInvestasi::where('user_id', $userId)
            ->where('nama', $namaAset)
            ->orderBy('tanggal_beli', 'desc')
            ->get()
            ->map(function ($item) {
                $tglBeli = Carbon::parse($item->tanggal_beli);
                $tglJual = $item->tanggal_jual ? Carbon::parse($item->tanggal_jual) : now();
                $item->durasi_days = max(1, $tglBeli->diffInDays($tglJual));
                $item->formatted_tgl_beli = $tglBeli->format('d/m/Y');
                $item->formatted_tgl_jual = $item->tanggal_jual ? Carbon::parse($item->tanggal_jual)->format('d/m/Y') : '-';
                return $item;
            });

        $detailKeterangan = $history->first()->detail ?? $history->first()->platform ?? 'Seluruh riwayat transaksi jual & beli aset ini';

        return response()->json([
            'nama' => $namaAset,
            'detail'  => $detailKeterangan,
            'history' => $history
        ]);
    }

    public function store(InvestasiRequest $request)
    {
        $this->investasiService->createInvestasi($request->validated(), Auth::id());
        return redirect()->back()->with('success', 'Catatan investasi berhasil disimpan!');
    }

    public function update(InvestasiRequest $request, $id)
    {
        $investasi = PencatatanInvestasi::where('user_id', Auth::id())->findOrFail($id);
        $this->investasiService->updateInvestasi($investasi, $request->validated());

        return redirect()->back()->with('success', 'Catatan investasi berhasil diperbarui!');
    }

    public function jual(InvestasiRequest $request, $id)
    {
        $investasi = PencatatanInvestasi::where('user_id', Auth::id())->findOrFail($id);
        $this->investasiService->jualInvestasi($investasi, $request->validated());

        return redirect()->back()->with('success', 'Aset berhasil dilikuidasi / dijual!');
    }

    public function destroy($id)
    {
        $investasi = PencatatanInvestasi::where('user_id', Auth::id())->findOrFail($id);
        $this->investasiService->deleteInvestasi($investasi);

        return redirect()->back()->with('success', 'Catatan investasi berhasil dihapus!');
    }
}
