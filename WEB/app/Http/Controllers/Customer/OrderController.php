<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Feedback;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items', 'deliveryPerson'])->where('user_id', Auth::id())->latest()->get();
        return view('customer.orders', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['items', 'deliveryPerson', 'address', 'payment', 'feedback'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('customer.order_tracking', compact('order'));
    }

    public function submitFeedback(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        Feedback::updateOrCreate(
            ['user_id' => Auth::id(), 'order_id' => $request->order_id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return back()->with('success', 'Thank you for your rating & feedback!');
    }
}
