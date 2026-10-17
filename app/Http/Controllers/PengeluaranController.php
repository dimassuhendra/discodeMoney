<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengeluaranRequest;
use App\Models\Pengeluaran;
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

        // Seluruh pengambilan data & kalkulasi statistik ditangani di Service
        $data = $this->pengeluaranService->getPengeluaranPageData($userId, $request->all());

        return view('module.pengeluaran.index', $data);
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
