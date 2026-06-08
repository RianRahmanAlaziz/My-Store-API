<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mainImage = $this->images->where('is_main', true)->first();

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'description' => $this->description,

            'brand' => [
                'id' => $this->brand?->id,
                'name' => $this->brand?->name,
                'slug' => $this->brand?->slug,
            ],

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],

            'image' => $mainImage
                ? Storage::url($mainImage->image)
                : null,

            'images' => $this->images->map(fn($image) => [
                'id' => $image->id,
                'image' => Storage::url($image->image),
                'is_main' => $image->is_main,
            ]),

            'variants' => $this->variants->map(fn($variant) => [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'stock' => $variant->stock,
            ]),

            'sizes' => $this->variants->pluck('size')->unique()->values(),
            'colors' => $this->variants->pluck('color')->unique()->values(),
            'rating' => round($this->reviews_avg_rating ?? $this->reviews()->avg('rating') ?? 0, 1),
            'reviews' => $this->reviews_count ?? $this->reviews()->count(),
            'is_new' => $this->is_new,
            'is_trending' => $this->is_trending,
            'is_best_seller' => $this->is_best_seller,
            'is_active' => $this->is_active,
            'is_wishlisted' => auth('sanctum')->check()
                ? $this->wishlists()
                ->where('user_id', auth('sanctum')->id())
                ->exists()
                : false,
        ];
    }
}
