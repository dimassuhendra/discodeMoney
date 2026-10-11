<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Services\BarangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangController extends Controller
{
    protected $barangService;

    public function __construct(BarangService $barangService)
    {
        $this->barangService = $barangService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $stats = $this->barangService->getBarangStats($userId, $startDate, $endDate);

        $query = Barang::with('pengeluaran')->where('user_id', $userId);

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_beli', [$startDate, $endDate]);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                    ->orWhere('tempat_beli', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('jenis_barang')) {
            $query->where('jenis_barang', $request->jenis_barang);
        }

        $barangList = $query->orderBy('tanggal_beli', 'desc')->orderBy('id', 'desc')->paginate(15);

        return view('module.barang.index', compact('stats', 'barangList'));
    }

    public function getHistoryByNama(Request $request)
    {
        $namaBarang = $request->input('nama');
        $userId = Auth::id();

        $data = $this->barangService->getItemHistory($userId, $namaBarang);

        return response()->json($data);
    }

    public function store(BarangRequest $request)
    {
        $this->barangService->createBarang($request->validated(), Auth::id());

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan ke inventaris!');
    }

    public function update(BarangRequest $request, $id)
    {
        $barang = Barang::where('user_id', Auth::id())->findOrFail($id);
        $this->barangService->updateBarang($barang, $request->validated());

        return redirect()->back()->with('success', 'Data barang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barang = Barang::where('user_id', Auth::id())->findOrFail($id);
        $this->barangService->deleteBarang($barang);

        return redirect()->back()->with('success', 'Barang berhasil dihapus!');
    }
}
