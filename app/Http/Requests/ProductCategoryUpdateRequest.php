<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => 'nullable|max:2048',
            'name' => 'required|string|max:255|unique:product_categories,name,' . $this->route('product_category'),
            'tagline' => 'nullable|string|max:255',
            'description' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'image' => 'Foto',
            'name' => 'Nama Kategori',
            'tagline' => 'Tagline',
            'description' => 'Deskripsi'
        ];
    }
}
