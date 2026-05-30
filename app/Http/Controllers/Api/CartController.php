<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CartController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $carts = Cart::query()
            ->with(['product.mainImage', 'product.brand', 'variant'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $total = $carts->sum(function ($cart) {
            return $cart->product->price * $cart->quantity;
        });

        return $this->success([
            'items' => CartResource::collection($carts),
            'summary' => [
                'total_items' => $carts->sum('quantity'),
                'subtotal' => (float) $total,
            ],
        ], 'Cart retrieved successfully');
    }

    public function store(StoreCartRequest $request)
    {
        if ($request->product_variant_id) {
            $variant = ProductVariant::where('id', $request->product_variant_id)
                ->where('product_id', $request->product_id)
                ->firstOrFail();

            if ($variant->stock < $request->quantity) {
                return $this->error('Stock is not enough', 422);
            }
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
            $message = 'Cart quantity updated successfully';
            $status = 200;
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
            ]);

            $message = 'Product added to cart successfully';
            $status = 201;
        }

        return $this->success(
            new CartResource($cart->load(['product.mainImage', 'product.brand', 'variant'])),
            $message,
            $status
        );
    }

    public function update(UpdateCartRequest $request, Cart $cart)
    {
        abort_if($cart->user_id !== $request->user()->id, 403);

        if ($cart->variant && $cart->variant->stock < $request->quantity) {
            return response()->json([
                'message' => 'Stock is not enough',
            ], 422);
        }

        $cart->update([
            'quantity' => $request->quantity,
        ]);

        return $this->success(
            new CartResource($cart->load(['product.mainImage', 'product.brand', 'variant'])),
            'Cart updated successfully'
        );
    }

    public function destroy(Request $request, Cart $cart)
    {
        abort_if($cart->user_id !== $request->user()->id, 403);

        $cart->delete();

        return $this->deleted('Cart item deleted successfully');
    }

    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return $this->deleted('Cart cleared successfully');
    }
}
