<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengeluaranRequest;
use App\Models\Pengeluaran;
use App\Models\SumberDana;
use App\Services\PengeluaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    protected $pengeluaranService;

    public function __construct(PengeluaranService $pengeluaranService)
    {
        $this->pengeluaranService = $pengeluaranService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pengeluaran::with(['sumberDana', 'pencatatanInvestasi', 'barang'])
            ->where('user_id', $userId);

        // Filter Search Keterangan
        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        // Filter Sumber Dana
        if ($request->filled('sumber_dana_id')) {
            $query->where('sumber_dana_id', $request->sumber_dana_id);
        }

        // Filter Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } else {
            // Default: Pengeluaran Bulan Ini
            $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
        }

        $pengeluaranList = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15);
        $sumberDanaList = SumberDana::where('user_id', $userId)->get();

        // Stats Insight Pengeluaran
        $stats = $this->pengeluaranService->getPengeluaranStats($userId);

        return view('module.pengeluaran.index', compact(
            'pengeluaranList',
            'sumberDanaList',
            'stats'
        ));
    }

    public function store(PengeluaranRequest $request)
    {
        $this->pengeluaranService->createPengeluaran($request->validated(), Auth::id());

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function update(PengeluaranRequest $request, $id)
    {
        $pengeluaran = Pengeluaran::where('user_id', Auth::id())->findOrFail($id);
        $this->pengeluaranService->updatePengeluaran($pengeluaran, $request->validated());

        return redirect()->back()->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::where('user_id', Auth::id())->findOrFail($id);
        $this->pengeluaranService->deletePengeluaran($pengeluaran);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dihapus!');
    }
}
