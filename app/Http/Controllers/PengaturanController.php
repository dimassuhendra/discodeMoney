<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengaturanProfileRequest;
use App\Services\PengaturanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    protected $pengaturanService;

    public function __construct(PengaturanService $pengaturanService)
    {
        $this->pengaturanService = $pengaturanService;
    }

    public function index()
    {
        $user = Auth::user();
        $stats = $this->pengaturanService->getAccountStats($user);

        return view('module.pengaturan.index', compact('user', 'stats'));
    }

    public function updateProfile(PengaturanProfileRequest $request)
    {
        $user = Auth::user();
        $this->pengaturanService->updateEmail($user, $request->email);

        return redirect()->back()->with('success', 'Email akun berhasil diperbarui!');
    }

    public function updateKodeAkses(PengaturanProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $this->pengaturanService->updateKodeAkses(
                $user,
                $request->kode_akses_lama,
                $request->kode_akses_baru
            );

            return redirect()->back()->with('success', 'Kode akses / PIN berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function exportData()
    {
        $userId = Auth::id();
        $csvContent = $this->pengaturanService->exportDataCsv($userId);
        $filename = 'backup_keuangan_' . date('Y_m_d_His') . '.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function resetData(PengaturanProfileRequest $request)
    {
        try {
            $user = Auth::user();
            $this->pengaturanService->resetAllData($user, $request->kode_akses_konfirmasi);

            return redirect()->back()->with('success', 'Seluruh data transaksi berhasil di-reset!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
