<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Route ubah email/profil
        if ($this->routeIs('pengaturan.update-profile')) {
            return [
                'email' => 'required|email|max:255|unique:users,email,' . $this->user()->id,
            ];
        }

        // Route ubah kode_akses (password/PIN)
        if ($this->routeIs('pengaturan.update-kode-akses')) {
            return [
                'kode_akses_lama' => 'required|string',
                'kode_akses_baru' => 'required|string|min:4|confirmed',
            ];
        }

        // Route reset data
        if ($this->routeIs('pengaturan.reset-data')) {
            return [
                'kode_akses_konfirmasi' => 'required|string',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'email.required'          => 'Alamat email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'email.unique'            => 'Email sudah digunakan oleh akun lain.',
            'kode_akses_lama.required' => 'Kode akses saat ini wajib diisi.',
            'kode_akses_baru.required' => 'Kode akses baru wajib diisi.',
            'kode_akses_baru.min'      => 'Kode akses baru minimal 4 karakter/digit.',
            'kode_akses_baru.confirmed' => 'Konfirmasi kode akses baru tidak cocok.',
            'kode_akses_konfirmasi.required' => 'Masukkan kode akses saat ini untuk mengonfirmasi reset data.',
        ];
    }
}
