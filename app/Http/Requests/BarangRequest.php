<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_barang'  => 'required|string|max:255',
            'jenis_barang' => 'required|in:barang_mati,barang_hidup,kebutuhan_rutin',
            'harga'        => 'required|numeric|min:0',
            'tempat_beli'  => 'nullable|string|max:255',
            'tanggal_beli' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_barang.required'  => 'Nama barang wajib diisi.',
            'jenis_barang.required' => 'Pilih jenis barang terlebih dahulu.',
            'harga.required'        => 'Harga / nilai barang wajib diisi.',
            'tanggal_beli.required' => 'Tanggal beli wajib diisi.',
        ];
    }
}
