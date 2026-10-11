<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemasukanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keterangan'     => 'required|string|max:255',
            'sumber_dana_id' => 'required|exists:sumber_dana,id',
            'jumlah'         => 'required|numeric|min:1',
            'tanggal'        => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'keterangan.required'     => 'Keterangan pemasukan wajib diisi.',
            'sumber_dana_id.required' => 'Pilih sumber dana / rekening penerima.',
            'sumber_dana_id.exists'   => 'Sumber dana yang dipilih tidak valid.',
            'jumlah.required'         => 'Nominal pemasukan wajib diisi.',
            'jumlah.min'              => 'Nominal pemasukan minimal Rp1.',
            'tanggal.required'        => 'Tanggal penerimaan wajib diisi.',
        ];
    }
}
