<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            'subtotal' => (float) $this->subtotal,
            'shipping_cost' => (float) $this->shipping_cost,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,

            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'order_status' => $this->order_status,

            'note' => $this->note,

            'address' => $this->address ? [
                'id' => $this->address->id,
                'receiver_name' => $this->address->receiver_name,
                'phone' => $this->address->phone,
                'address' => $this->address->address,
                'province' => $this->address->province,
                'city' => $this->address->city,
                'district' => $this->address->district,
                'postal_code' => $this->address->postal_code,
            ] : null,

            'items' => OrderItemResource::collection($this->whenLoaded('items')),

            'created_at' => $this->created_at,
        ];
    }
}
