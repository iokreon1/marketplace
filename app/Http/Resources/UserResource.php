<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource 
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role = $this->whenLoaded('roles', function () {
            return $this->roles->first()?->name ?? '-';
        }, '-');

        return [
            'id' => $this->id,
            'profile_picture' => $this->profile_picture ? asset('storage/' . $this->profile_picture) : null,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $role,
            'permissions' => $this->permissions ?? [],
            'token' => $this->when(isset($this->token), $this->token),
            'store' => $this->when(
                $role === 'store',
                $this->whenLoaded('store', fn() => new StoreResource($this->store))
            ),
            'buyer' => $this->when(
                $role === 'buyer',
                $this->whenLoaded('buyer', fn() => new BuyerResource($this->buyer))
            ),
        ];
    }
}
