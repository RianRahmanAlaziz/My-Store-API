<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishlistRequest;
use App\Http\Resources\WishlistResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class WishlistController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $wishlists = Wishlist::query()
            ->with(['product.images', 'product.brand', 'product.category'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return $this->success(
            WishlistResource::collection($wishlists),
            'Wishlist retrieved successfully'
        );
    }

    public function store(StoreWishlistRequest $request)
    {
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return $this->created(
            new WishlistResource(
                $wishlist->load(['product.images', 'product.brand', 'product.category'])
            ),
            'Product added to wishlist successfully'
        );
    }

    public function destroy(Request $request, Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);

        $wishlist->delete();

        return $this->deleted('Wishlist deleted successfully');
    }

    public function destroyByProduct(Request $request, Product $product)
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $wishlist->delete();

        return $this->deleted('Wishlist deleted successfully');
    }
}
