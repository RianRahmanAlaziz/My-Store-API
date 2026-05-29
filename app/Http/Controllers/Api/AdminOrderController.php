<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['user', 'items', 'address'])
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->order_status, function ($query, $status) {
                $query->where('order_status', $status);
            })
            ->when($request->payment_status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        return new OrderResource(
            $order->load(['user', 'items', 'address'])
        );
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update([
            'order_status' => $request->order_status,
        ]);

        return new OrderResource(
            $order->load(['user', 'items', 'address'])
        );
    }

    public function updatePaymentStatus(UpdatePaymentStatusRequest $request, Order $order)
    {
        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        return new OrderResource(
            $order->load(['user', 'items', 'address'])
        );
    }
}
