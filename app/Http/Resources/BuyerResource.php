<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'phone_number' => $this->phone_number,
            'address_id' => $this->address_id,
            'city' => $this->city,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
        ];
    }
}
