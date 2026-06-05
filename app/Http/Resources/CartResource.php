<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price = $this->product->price;
        $subtotal = $price * $this->quantity;

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'subtotal' => (float) $subtotal,

            'product' => [
                'id' => $this->product->id,
                'slug' => $this->product->slug,
                'name' => $this->product->name,
                'price' => (float) $this->product->price,
                'image' => $this->product->mainImage?->image,
                'brand' => $this->product->brand?->name,
                'variants' => $this->product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'size' => $variant->size,
                        'color' => $variant->color,
                        'stock' => $variant->stock,
                    ];
                }),
            ],

            'variant' => $this->variant ? [
                'id' => $this->variant->id,
                'size' => $this->variant->size,
                'color' => $this->variant->color,
                'stock' => $this->variant->stock,
            ] : null,
        ];
    }
}
