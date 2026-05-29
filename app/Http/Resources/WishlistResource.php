<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainImage = $this->product->images->where('is_main', true)->first();

        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'slug' => $this->product->slug,
                'name' => $this->product->name,
                'price' => (float) $this->product->price,
                'original_price' => $this->product->original_price
                    ? (float) $this->product->original_price
                    : null,
                'image' => $mainImage?->image,
                'brand' => $this->product->brand?->name,
                'category' => $this->product->category?->name,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
