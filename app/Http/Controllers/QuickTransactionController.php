<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickTransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Handle simpan dari Quick Add Modal
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'pengeluaran');

        if ($type === 'pengeluaran') {
            $validated = $request->validate([
                'tanggal'        => 'required|date',
                'jumlah'         => 'required|numeric|min:1',
                'sumber_dana_id' => 'required|exists:sumber_dana,id',
                'keterangan'     => 'required|string|max:255',
                'is_barang'      => 'nullable|boolean',
                'jenis_barang'   => 'nullable|in:barang_mati,barang_hidup',
                'tempat_beli'    => 'nullable|string|max:255',
            ]);

            $this->transactionService->storePengeluaran($validated, Auth::id());
            $message = 'Pengeluaran berhasil dicatat!';
        } else {
            $validated = $request->validate([
                'tanggal'        => 'required|date',
                'jumlah'         => 'required|numeric|min:1',
                'sumber_dana_id' => 'nullable|exists:sumber_dana,id',
                'keterangan'     => 'required|string|max:255',
            ]);

            $this->transactionService->storePemasukan($validated, Auth::id());
            $message = 'Pemasukan berhasil dicatat!';
        }

        return redirect()->back()->with('success', $message);
    }
}
