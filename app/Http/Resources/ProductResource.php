<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'store' => new StoreResource($this->whenLoaded('store')),
            'product_category' => new ProductCategoryResource($this->whenLoaded('productCategory')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'condition' => $this->condition,
            'price' => (float) (string) $this->price,
            'weight' => (float) (string) $this->weight,
            'stock' => $this->stock,
            'created_at' => $this->created_at,
            'sold_count' => (int) $this->transactionDetails()->whereHas('transaction', function ($query) {
                $query->where('payment_status', 'paid');
            })->sum('qty'),
            'product_images' => ProductImageResource::collection($this->whenLoaded('productImages')),
            'product_reviews' => ProductReviewResource::collection($this->whenLoaded('productReviews'))
        ];
    }
}
