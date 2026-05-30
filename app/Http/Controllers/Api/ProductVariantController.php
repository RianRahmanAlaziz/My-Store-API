<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\ApiResponse;


class ProductVariantController extends Controller
{
    use ApiResponse;

    public function index(Product $product)
    {
        $variants = $product->variants()->latest()->get();

        return $this->success(
            ProductVariantResource::collection($variants),
            'Product variants retrieved successfully'
        );
    }

    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $variant = $product->variants()->create([
            'size' => $request->size,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return $this->created(
            new ProductVariantResource($variant),
            'Product variant created successfully'
        );
    }

    public function show(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        return $this->success(
            new ProductVariantResource($variant),
            'Product variant retrieved successfully'
        );
    }

    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ) {
        abort_if($variant->product_id !== $product->id, 404);

        $variant->update([
            'size' => $request->size,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return $this->success(
            new ProductVariantResource($variant),
            'Product variant updated successfully'
        );
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        $variant->delete();

        return $this->deleted('Product variant deleted successfully');
    }
}
