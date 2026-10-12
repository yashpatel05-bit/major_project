<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryPerson;

class DeliveryPersonController extends Controller
{
    public function index()
    {
        $deliveryPersons = DeliveryPerson::withCount(['orders' => function($q) {
            $q->where('order_status', 'Out for Delivery');
        }])->latest()->get();
        return view('admin.delivery.index', compact('deliveryPersons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|unique:delivery_persons,email',
            'vehicle_number' => 'nullable|string|max:50',
        ]);

        DeliveryPerson::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'vehicle_number' => $request->vehicle_number,
            'status' => 'available'
        ]);

        return back()->with('success', 'Delivery personnel added successfully!');
    }

    public function update(Request $request, $id)
    {
        $person = DeliveryPerson::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:191',
            'phone' => 'required|string|max:50',
            'status' => 'required|in:available,on_delivery,offline'
        ]);

        $person->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'vehicle_number' => $request->vehicle_number,
            'status' => $request->status,
        ]);

        return back()->with('success', 'Delivery personnel updated successfully!');
    }

    public function destroy($id)
    {
        $person = DeliveryPerson::findOrFail($id);
        $person->delete();
        return back()->with('success', 'Delivery personnel removed successfully!');
    }
}
