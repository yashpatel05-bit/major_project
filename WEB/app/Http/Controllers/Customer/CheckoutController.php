<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Payment;
use App\Models\DeliveryPerson;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu')->with('error', 'Your cart is empty!');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $tax = $subtotal * 0.05;
        $deliveryFee = 40;
        $total = $subtotal + $tax + $deliveryFee;

        $addresses = Address::where('user_id', Auth::id())->get();

        return view('customer.checkout', compact('cart', 'subtotal', 'tax', 'deliveryFee', 'total', 'addresses'));
    }

    public function placeOrder(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('customer.menu')->with('error', 'Your cart is empty!');
        }

        $request->validate([
            'address_id' => 'required',
            'payment_method' => 'required|in:COD,Razorpay',
        ]);

        if ($request->payment_method === 'Razorpay' && empty($request->razorpay_payment_id)) {
            return back()->with('error', 'Online payment failed or was cancelled. Please try paying again.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $tax = $subtotal * 0.05;
        $deliveryFee = 40;
        $total = $subtotal + $tax + $deliveryFee;

        // Delivery partner must be assigned manually by Admin (do not auto-assign)
        $order = Order::create([
            'order_number' => 'SNB-' . strtoupper(substr(uniqid(), -6)),
            'user_id' => Auth::id(),
            'delivery_person_id' => null,
            'address_id' => $request->address_id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_method === 'Razorpay' ? 'paid' : 'pending',
            'razorpay_payment_id' => $request->razorpay_payment_id ?: null,
            'order_status' => 'Pending',
            'notes' => $request->notes
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['id'],
                'item_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        }

        if ($request->payment_method === 'Razorpay') {
            Payment::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id ?: 'order_' . uniqid(),
                'amount' => $total,
                'payment_method' => 'Razorpay',
                'status' => 'Success'
            ]);
        }

        session()->forget('cart');

        return redirect()->route('customer.orders.show', $order->id)->with('success', 'Order placed successfully!');
    }
}
