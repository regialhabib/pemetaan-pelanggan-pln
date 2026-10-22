<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePelangganRequest extends FormRequest
{
    /**
     * Nilai enum kolom `pelanggans.golongan_tarif`.
     */
    public const array GOLONGAN_TARIF = ['R1', 'R2', 'R3', 'INDUSTRI'];

    /**
     * Akses sudah dibatasi oleh middleware `admin` pada route.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_pelanggan' => ['required', 'string', 'max:255', 'unique:pelanggans,id_pelanggan'],
            ...self::attributeRules(),
        ];
    }

    /**
     * Aturan untuk atribut pelanggan yang sama antara tambah dan ubah.
     *
     * @return array<string, array<int, string>>
     */
    public static function attributeRules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'golongan_tarif' => ['required', 'in:'.implode(',', self::GOLONGAN_TARIF)],
            'daya' => ['required', 'integer', 'min:0'],
        ];
    }
}
