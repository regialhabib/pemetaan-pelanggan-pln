<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\TugasKunjungan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTugasKunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', TugasKunjungan::class);
    }

    /**
     * Form mengirim daftar ID pelanggan sebagai string JSON.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->pelanggan)) {
            $this->merge(['pelanggan' => json_decode($this->pelanggan, true)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pelanggan' => ['required', 'array', 'min:1'],
            'pelanggan.*' => ['integer', 'distinct', 'exists:pelanggans,id'],
            'petugas' => ['required', Rule::exists('users', 'id')->where('role', Role::Petugas->value)],
            'keterangan' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pelanggan.required' => 'Pilih minimal satu pelanggan.',
            'pelanggan.*.exists' => 'Ada pelanggan yang tidak ditemukan.',
            'petugas.exists' => 'Petugas tidak valid.',
        ];
    }
}
