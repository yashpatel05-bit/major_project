<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeliveryPerson;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'deliveryPerson', 'items.menuItem', 'address']);

        if ($request->has('status') && $request->status != 'all') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->latest()->get();
        $deliveryPersons = DeliveryPerson::all();

        return view('admin.orders.index', compact('orders', 'deliveryPersons'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'deliveryPerson', 'items', 'address', 'payment', 'feedback'])->findOrFail($id);
        $deliveryPersons = DeliveryPerson::all();
        return view('admin.orders.show', compact('order', 'deliveryPersons'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'order_status' => 'required|in:Pending,Preparing,Out for Delivery,Delivered,Cancelled',
        ]);

        $currentStatus = $order->order_status;
        $newStatus = $request->order_status;

        // Prevent invalid backward status regressions
        if (in_array($currentStatus, ['Delivered', 'Cancelled'])) {
            return back()->with('error', "Order #{$order->order_number} is already {$currentStatus} and cannot be modified.");
        }
        if ($currentStatus === 'Preparing' && $newStatus === 'Pending') {
            return back()->with('error', "Cannot revert order #{$order->order_number} back to Pending once Preparing.");
        }
        if ($currentStatus === 'Out for Delivery' && in_array($newStatus, ['Pending', 'Preparing'])) {
            return back()->with('error', "Cannot revert order #{$order->order_number} back to {$newStatus} once Out for Delivery.");
        }

        $order->order_status = $newStatus;
        
        if ($request->has('delivery_person_id')) {
            $order->delivery_person_id = $request->delivery_person_id;
            
            // If assigned and status is Out for Delivery, update delivery person status
            if ($request->delivery_person_id) {
                $dp = DeliveryPerson::find($request->delivery_person_id);
                if ($dp) {
                    $dp->status = ($newStatus === 'Delivered' || $newStatus === 'Cancelled') 
                        ? 'available' 
                        : 'on_delivery';
                    $dp->save();
                }
            }
        }

        if ($newStatus === 'Delivered' && $order->payment_method === 'COD') {
            $order->payment_status = 'paid';
        }

        $order->save();

        return back()->with('success', "Order #{$order->order_number} updated to {$newStatus}!");
    }
}
