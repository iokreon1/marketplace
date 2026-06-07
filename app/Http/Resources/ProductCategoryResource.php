<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'parent' => new ProductCategoryResource($this->whenLoaded('parent')), 
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'childerns' => ProductCategoryResource::collection($this->whenLoaded('childerns')),
            'product_count' => $this->when(isset($this->products_count), $this->products_count),
            'children_count' => $this->when(isset($this->children_count), $this->children_count),
        ];
    }
}
