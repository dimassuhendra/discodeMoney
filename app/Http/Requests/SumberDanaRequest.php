<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SumberDanaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'          => 'required|string|max:255',
            'budget'        => 'nullable|numeric|min:0',
            'budget_harian' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'   => 'Nama sumber dana wajib diisi.',
            'budget.numeric' => 'Nominal budget harus berupa angka.',
            'budget_harian.numeric' => 'Budget harian harus berupa angka.',
        ];
    }
}
