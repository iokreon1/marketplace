<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class WithdrawalApproveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'proof' => 'required|image|mimes:png,jpg|max:2048'
        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'proof' => 'Bukti Transfer'
        ];
    }
}
