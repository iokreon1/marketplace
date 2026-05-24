<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class BuyerStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'profile_picture' => 'required|image|mimes:png,jpg',
            'phone_number' => 'required|string'
        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'user_id' => 'User',
            'profile_picture' => 'Avatar',
            'phone_number' => 'Nomor HP'
        ];
    }
}
