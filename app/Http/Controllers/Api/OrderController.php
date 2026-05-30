<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class OrderController extends Controller
{
    use ApiResponse;


    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $orders = Order::query()
            ->with(['items', 'address'])
            ->where('user_id', $request->user()->id)
            ->when(
                $request->order_status,
                fn($query, $status) =>
                $query->where('order_status', $status)
            )
            ->when(
                $request->payment_status,
                fn($query, $status) =>
                $query->where('payment_status', $status)
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated(
            OrderResource::collection($orders),
            'Orders retrieved successfully'
        );
    }

    public function show(Request $request, Order $order)
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        return $this->success(
            new OrderResource($order->load(['items', 'address'])),
            'Order retrieved successfully'
        );
    }
}
