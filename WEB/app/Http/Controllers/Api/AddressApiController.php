<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressApiController extends Controller
{
    public function getUserAddresses($userId)
    {
        $addresses = Address::where('user_id', $userId)->get();
        return response()->json([
            'success' => true,
            'data' => $addresses
        ]);
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'address_line' => 'required|string',
        ]);

        $address = Address::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'address_line' => $request->address_line,
            'city' => $request->city ?: 'City Center',
            'pincode' => $request->pincode,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_default' => $request->is_default ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ], 201);
    }

    public function deleteAddress($id)
    {
        $address = Address::find($id);
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found'], 404);
        }
        $address->delete();
        return response()->json(['success' => true, 'message' => 'Address deleted']);
    }
}
