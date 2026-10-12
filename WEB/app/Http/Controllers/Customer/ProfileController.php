<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();
        return view('customer.profile', compact('user', 'addresses'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if ($request->filled('password')) {
            $user->password = $request->password; // Plain text
            $user->save();
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'address_line' => 'required|string',
        ]);

        Address::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'address_line' => $request->address_line,
            'city' => $request->city ?: 'City Center',
            'pincode' => $request->pincode,
        ]);

        return back()->with('success', 'New delivery address added!');
    }
}
