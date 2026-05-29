<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Http\Resources\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;


class ProductVariantController extends Controller
{
    public function index(Product $product)
    {
        return ProductVariantResource::collection(
            $product->variants()->latest()->get()
        );
    }

    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $variant = $product->variants()->create([
            'size' => $request->size,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return new ProductVariantResource($variant);
    }

    public function show(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        return new ProductVariantResource($variant);
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

        return new ProductVariantResource($variant);
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        $variant->delete();

        return response()->json([
            'message' => 'Product variant deleted successfully',
        ]);
    }
}
