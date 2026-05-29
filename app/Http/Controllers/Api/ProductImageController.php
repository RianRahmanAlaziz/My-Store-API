<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    public function index(Product $product)
    {
        return ProductImageResource::collection(
            $product->images()->latest()->get()
        );
    }

    public function store(StoreProductImageRequest $request, Product $product)
    {
        if ($request->boolean('is_main')) {
            $product->images()->update(['is_main' => false]);
        }

        $image = $product->images()->create([
            'image' => $request->image,
            'is_main' => $request->boolean('is_main'),
        ]);

        return new ProductImageResource($image);
    }

    public function show(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 404);

        return new ProductImageResource($image);
    }

    public function update(
        UpdateProductImageRequest $request,
        Product $product,
        ProductImage $image
    ) {
        abort_if($image->product_id !== $product->id, 404);

        if ($request->boolean('is_main')) {
            $product->images()->where('id', '!=', $image->id)->update([
                'is_main' => false,
            ]);
        }

        $image->update([
            'image' => $request->image,
            'is_main' => $request->boolean('is_main'),
        ]);

        return new ProductImageResource($image);
    }

    public function destroy(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 404);

        $image->delete();

        return response()->json([
            'message' => 'Product image deleted successfully',
        ]);
    }
}
