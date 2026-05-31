<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class ProductReviewStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'transaction_id' => 'required|string|exists:transactions,id',
            'product_id' => 'required|string|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'transaction_id' => 'Transaksi',
            'product_id' => 'Produk',
            'rating' => 'Rating',
            'review' => 'Review'
        ];
    }
}
