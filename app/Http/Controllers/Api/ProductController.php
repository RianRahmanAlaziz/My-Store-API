<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'brand', 'images', 'variants'])
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->when($request->brand, function ($query, $brand) {
                $query->whereHas('brand', function ($q) use ($brand) {
                    $q->where('slug', $brand);
                });
            })
            ->when($request->sort === 'low-price', function ($query) {
                $query->orderBy('price', 'asc');
            })
            ->when($request->sort === 'high-price', function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->when($request->sort === 'latest', function ($query) {
                $query->latest();
            })
            ->paginate(12);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'description' => $request->description,
            'is_new' => $request->boolean('is_new'),
            'is_trending' => $request->boolean('is_trending'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return new ProductResource(
            $product->load(['category', 'brand', 'images', 'variants'])
        );
    }

    public function show(Product $product)
    {
        return new ProductResource(
            $product->load(['category', 'brand', 'images', 'variants'])
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update([
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sku' => $request->sku,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'description' => $request->description,
            'is_new' => $request->boolean('is_new'),
            'is_trending' => $request->boolean('is_trending'),
            'is_best_seller' => $request->boolean('is_best_seller'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return new ProductResource(
            $product->load(['category', 'brand', 'images', 'variants'])
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
