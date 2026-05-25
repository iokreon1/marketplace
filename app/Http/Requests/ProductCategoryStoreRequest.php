<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class ProductCategoryStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image:mimes:png,jpg|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'parent_id' => 'Kategori Induk',
            'image' => 'Foto',
            'name' => 'Nama Kategori',
            'tagline' => 'Tagline',
            'description' => 'Deskripsi'
        ];
    }
}
