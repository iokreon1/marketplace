<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreBalanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array // data disini akan diubah menjadi array untuk dikirim sebagai response JSON, bisa cek di Postman 
    {
        return [
            'id' => $this->id,
            'store' => new StoreResource($this->store),
            'balance' => (float) (string) $this->balance, // cast ke string dulu baru ke float untuk menghindari masalah presisi pada angka desimal,
        ];
    }
}
