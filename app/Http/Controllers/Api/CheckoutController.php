<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request)
    {
        $user = $request->user();

        $address = Address::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $carts = Cart::query()
            ->with(['product', 'variant'])
            ->where('user_id', $user->id)
            ->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
            ], 422);
        }

        foreach ($carts as $cart) {
            if ($cart->variant && $cart->variant->stock < $cart->quantity) {
                return response()->json([
                    'message' => 'Stock is not enough for ' . $cart->product->name,
                ], 422);
            }
        }

        $subtotal = $carts->sum(function ($cart) {
            return $cart->product->price * $cart->quantity;
        });

        $shippingCost = $subtotal > 500000 ? 0 : 25000;
        $discount = 0;
        $total = $subtotal + $shippingCost - $discount;

        $order = DB::transaction(function () use (
            $request,
            $user,
            $address,
            $carts,
            $subtotal,
            $shippingCost,
            $discount,
            $total
        ) {
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'invoice_number' => 'INV-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'note' => $request->note,
            ]);

            foreach ($carts as $cart) {
                $order->items()->create([
                    'product_id' => $cart->product_id,
                    'product_variant_id' => $cart->product_variant_id,
                    'product_name' => $cart->product->name,
                    'variant_size' => $cart->variant?->size,
                    'variant_color' => $cart->variant?->color,
                    'price' => $cart->product->price,
                    'quantity' => $cart->quantity,
                    'subtotal' => $cart->product->price * $cart->quantity,
                ]);

                if ($cart->variant) {
                    ProductVariant::where('id', $cart->variant->id)
                        ->decrement('stock', $cart->quantity);
                }
            }

            Cart::where('user_id', $user->id)->delete();

            return $order;
        });

        return new OrderResource(
            $order->load(['items', 'address'])
        );
    }
}
