<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;


    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 12), 50);

        $products = Product::query()
            ->with(['category', 'brand', 'images', 'variants'])
            ->when(
                $request->boolean('active_only', true),
                fn($query) =>
                $query->where('is_active', true)
            )
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->category,
                fn($query, $category) =>
                $query->whereHas('category', fn($q) => $q->where('slug', $category))
            )
            ->when(
                $request->brand,
                fn($query, $brand) =>
                $query->whereHas('brand', fn($q) => $q->where('slug', $brand))
            )
            ->when(
                $request->min_price,
                fn($query, $price) =>
                $query->where('price', '>=', $price)
            )
            ->when(
                $request->max_price,
                fn($query, $price) =>
                $query->where('price', '<=', $price)
            )
            ->when(
                $request->filled('is_new'),
                fn($query) =>
                $query->where('is_new', $request->boolean('is_new'))
            )
            ->when(
                $request->filled('is_trending'),
                fn($query) =>
                $query->where('is_trending', $request->boolean('is_trending'))
            )
            ->when(
                $request->filled('is_best_seller'),
                fn($query) =>
                $query->where('is_best_seller', $request->boolean('is_best_seller'))
            )
            ->when($request->sort === 'low-price', fn($query) => $query->orderBy('price', 'asc'))
            ->when($request->sort === 'high-price', fn($query) => $query->orderBy('price', 'desc'))
            ->when($request->sort === 'oldest', fn($query) => $query->oldest())
            ->when(! in_array($request->sort, ['low-price', 'high-price', 'oldest']), fn($query) => $query->latest())
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
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

        return $this->created(
            new ProductResource($product->load(['category', 'brand', 'images', 'variants'])),
            'Product created successfully'
        );
    }

    public function show(Product $product)
    {
        return $this->success(
            new ProductResource($product->load(['category', 'brand', 'images', 'variants'])),
            'Product retrieved successfully'
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

        return $this->success(
            new ProductResource($product->load(['category', 'brand', 'images', 'variants'])),
            'Product updated successfully'
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->deleted('Product deleted successfully');
    }
}
