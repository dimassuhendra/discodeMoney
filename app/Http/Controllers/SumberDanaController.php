<?php

namespace App\Http\Controllers;

use App\Http\Requests\SumberDanaRequest;
use App\Models\SumberDana;
use App\Services\SumberDanaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SumberDanaController extends Controller
{
    protected $sumberDanaService;

    public function __construct(SumberDanaService $sumberDanaService)
    {
        $this->sumberDanaService = $sumberDanaService;
    }

    public function index()
    {
        $userId = Auth::id();
        $sumberDanaList = $this->sumberDanaService->getSumberDanaSummary($userId);

        $totalKas = $sumberDanaList->sum('saldo_aktif');
        $totalSumberDana = $sumberDanaList->count();

        return view('module.sumber_dana.index', compact(
            'sumberDanaList',
            'totalKas',
            'totalSumberDana'
        ));
    }

    public function store(SumberDanaRequest $request)
    {
        $this->sumberDanaService->createSumberDana($request->validated(), Auth::id());

        return redirect()->back()->with('success', 'Sumber dana berhasil ditambahkan!');
    }

    public function update(SumberDanaRequest $request, $id)
    {
        $sumberDana = SumberDana::where('user_id', Auth::id())->findOrFail($id);
        $this->sumberDanaService->updateSumberDana($sumberDana, $request->validated());

        return redirect()->back()->with('success', 'Sumber dana berhasil diperbarui!');
    }

    public function destroy($id)
    {
        try {
            $sumberDana = SumberDana::where('user_id', Auth::id())->findOrFail($id);
            $this->sumberDanaService->deleteSumberDana($sumberDana);

            return redirect()->back()->with('success', 'Sumber dana berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
