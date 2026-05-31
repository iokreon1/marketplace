<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tracking_number' => 'nullable|string',
            'delivery_proof' => 'nullable|image|mimes:png,jpg',
            'delivery_status' => 'required|in:processing,delivering,completed'
        ];
    }

    public function attributes()
    {
        return [
            'tracking_number' => 'Nomor Resi',
            'delivery_proof' => 'Bukti Pengiriman',
            'delivery_status' => 'Status Pengiriman'
        ];
    }
}
