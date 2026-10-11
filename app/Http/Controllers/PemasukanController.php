<?php

namespace App\Http\Controllers;

use App\Http\Requests\PemasukanRequest;
use App\Models\Pemasukan;
use App\Models\SumberDana;
use App\Services\PemasukanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    protected $pemasukanService;

    public function __construct(PemasukanService $pemasukanService)
    {
        $this->pemasukanService = $pemasukanService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $stats = $this->pemasukanService->getPemasukanStats($userId, $startDate, $endDate);
        $sumberDanaList = SumberDana::where('user_id', $userId)->get();

        $query = Pemasukan::with('sumberDana')->where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('sumber_dana_id')) {
            $query->where('sumber_dana_id', $request->sumber_dana_id);
        }

        $pemasukanList = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15);

        return view('module.pemasukan.index', compact('stats', 'sumberDanaList', 'pemasukanList'));
    }

    public function store(PemasukanRequest $request)
    {
        $this->pemasukanService->createPemasukan($request->validated(), Auth::id());

        return redirect()->back()->with('success', 'Transaksi pemasukan berhasil dicatat!');
    }

    public function update(PemasukanRequest $request, $id)
    {
        $pemasukan = Pemasukan::where('user_id', Auth::id())->findOrFail($id);
        $this->pemasukanService->updatePemasukan($pemasukan, $request->validated());

        return redirect()->back()->with('success', 'Transaksi pemasukan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::where('user_id', Auth::id())->findOrFail($id);
        $this->pemasukanService->deletePemasukan($pemasukan);

        return redirect()->back()->with('success', 'Transaksi pemasukan berhasil dihapus!');
    }
}
