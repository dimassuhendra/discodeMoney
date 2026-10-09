<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal'        => 'required|date',
            'jumlah'         => 'required|numeric|min:1',
            'sumber_dana_id' => 'required|exists:sumber_dana,id',
            'keterangan'     => 'required|string|max:255',
            'is_barang'      => 'nullable|boolean',
            'jenis_barang'   => 'nullable|in:barang_mati,barang_hidup',
            'tempat_beli'    => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required'        => 'Tanggal transaksi wajib diisi.',
            'jumlah.required'         => 'Nominal pengeluaran wajib diisi.',
            'sumber_dana_id.required' => 'Pilih sumber dana terlebih dahulu.',
            'keterangan.required'     => 'Keterangan pengeluaran wajib diisi.',
        ];
    }
}
