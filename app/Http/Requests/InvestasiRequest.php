<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('patch') || $this->routeIs('*.jual')) {
            return [
                'tanggal_jual'   => 'required|date',
                'harga_jual'     => 'nullable|numeric|min:0',
                'harga_jual_idr' => 'nullable|numeric|min:0',
            ];
        }

        return [
            'nama'            => 'required|string|max:255',
            'detail'          => 'nullable|string|max:255',
            'jenis_investasi' => 'required|string',
            'platform'        => 'nullable|string|max:255',
            'tanggal_beli'    => 'required|date',
            'tanggal_jual'    => 'nullable|date',
            'harga_beli'      => 'nullable|numeric|min:0',
            'harga_jual'      => 'nullable|numeric|min:0',
            'harga_beli_idr'  => 'nullable|numeric|min:0',
            'harga_jual_idr'  => 'nullable|numeric|min:0',
            'status'          => 'required|in:masih_dimiliki,sudah_dijual',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'            => 'Nama investasi wajib diisi.',
            'jenis_investasi.required' => 'Pilih jenis investasi.',
            'tanggal_beli.required'    => 'Tanggal beli wajib diisi.',
            'status.required'          => 'Status aset wajib dipilih.',
        ];
    }
}
