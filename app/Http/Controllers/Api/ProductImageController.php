<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Traits\ApiResponse;

class ProductImageController extends Controller
{
    use ApiResponse;

    public function index(Product $product)
    {
        $images = $product->images()->latest()->get();

        return $this->success(
            ProductImageResource::collection($images),
            'Product images retrieved successfully'
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

        return $this->created(
            new ProductImageResource($image),
            'Product image created successfully'
        );
    }

    public function show(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 404);

        return $this->success(
            new ProductImageResource($image),
            'Product image retrieved successfully'
        );
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

        return $this->success(
            new ProductImageResource($image),
            'Product image updated successfully'
        );
    }

    public function destroy(Product $product, ProductImage $image)
    {
        abort_if($image->product_id !== $product->id, 404);

        $image->delete();

        return $this->deleted('Product image deleted successfully');
    }
}
