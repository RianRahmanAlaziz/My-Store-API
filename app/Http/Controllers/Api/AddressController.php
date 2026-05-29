<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return AddressResource::collection($addresses);
    }

    public function store(StoreAddressRequest $request)
    {
        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)->update([
                'is_default' => false,
            ]);
        }

        $address = Address::create([
            'user_id' => $request->user()->id,
            'label' => $request->label,
            'receiver_name' => $request->receiver_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'postal_code' => $request->postal_code,
            'is_default' => $request->boolean('is_default'),
        ]);

        return new AddressResource($address);
    }

    public function show(Request $request, Address $address)
    {
        abort_if($address->user_id !== $request->user()->id, 403);

        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        abort_if($address->user_id !== $request->user()->id, 403);

        if ($request->boolean('is_default')) {
            Address::where('user_id', $request->user()->id)
                ->where('id', '!=', $address->id)
                ->update([
                    'is_default' => false,
                ]);
        }

        $address->update([
            'label' => $request->label,
            'receiver_name' => $request->receiver_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'postal_code' => $request->postal_code,
            'is_default' => $request->boolean('is_default'),
        ]);

        return new AddressResource($address);
    }

    public function destroy(Request $request, Address $address)
    {
        abort_if($address->user_id !== $request->user()->id, 403);

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully',
        ]);
    }

    public function setDefault(Request $request, Address $address)
    {
        abort_if($address->user_id !== $request->user()->id, 403);

        Address::where('user_id', $request->user()->id)->update([
            'is_default' => false,
        ]);

        $address->update([
            'is_default' => true,
        ]);

        return new AddressResource($address);
    }
}
