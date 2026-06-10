<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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
            'transaction_id' => $this->transaction_id,
            'product_id' => $this->product_id,
            'rating' => (int) $this->rating,
            'review' => $this->review,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'created_at' => $this->created_at,
            'buyer' => [
                'name' => $this->transaction?->buyer?->user?->name ?? 'Pembeli',
                'profile_picture' => $this->transaction?->buyer?->user?->profile_picture ? asset('storage/' . $this->transaction?->buyer?->user?->profile_picture) : null,
            ],
            'product' => new ProductResource($this->whenLoaded('product')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
        ];
    }
}
