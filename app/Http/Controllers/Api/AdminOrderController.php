<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class AdminOrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $orders = Order::query()
            ->with(['user', 'items', 'address'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
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
            'Admin orders retrieved successfully'
        );
    }

    public function show(Order $order)
    {
        return $this->success(
            new OrderResource($order->load(['user', 'items', 'address'])),
            'Order detail retrieved successfully'
        );
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update([
            'order_status' => $request->order_status,
        ]);

        return $this->success(
            new OrderResource($order->load(['user', 'items', 'address'])),
            'Order status updated successfully'
        );
    }

    public function updatePaymentStatus(UpdatePaymentStatusRequest $request, Order $order)
    {
        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        return $this->success(
            new OrderResource($order->load(['user', 'items', 'address'])),
            'Payment status updated successfully'
        );
    }
}
