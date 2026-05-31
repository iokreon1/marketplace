<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class TransactionStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'buyer_id' => 'required|string|exists:buyers,id',
            'store_id' => 'required|string|exists:stores,id',
            'address_id' => 'required|integer',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'shipping' => 'required',
            'shipping_type' => 'required',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|string|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'buyer_id' => 'Pembeli',
            'store_id' => 'Toko',
            'address_id' => 'Alamat',
            'address' => 'Alamat',
            'city' => 'Kota',
            'postal_code' => 'Kode Pos',
            'shipping' => 'Pengiriman',
            'shipping_type' => 'Jenis Pengiriman',
        ];  
    }
}
