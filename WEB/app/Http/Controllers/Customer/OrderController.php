<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Payment;
use App\Models\DeliveryPerson;
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

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($order->order_status, ['Delivered', 'Cancelled'])) {
            return back()->with('error', "Order cannot be cancelled as it is already {$order->order_status}.");
        }

        $request->validate([
            'razorpay_payment_id' => 'required|string',
        ], [
            'razorpay_payment_id.required' => 'Online cancellation fee of ₹50 is required to cancel this order.'
        ]);

        // Release assigned delivery partner if any
        if ($order->delivery_person_id) {
            $dp = DeliveryPerson::find($order->delivery_person_id);
            if ($dp) {
                $dp->status = 'available';
                $dp->save();
            }
        }

        $order->order_status = 'Cancelled';
        $order->cancellation_fee = 50.00;
        $order->cancellation_payment_id = $request->razorpay_payment_id;
        $order->cancellation_reason = 'Cancelled by customer on tracking screen';
        $order->cancelled_at = now();
        $order->save();

        Payment::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id ?: 'cancel_' . uniqid(),
            'amount' => 50.00,
            'payment_method' => 'Razorpay',
            'status' => 'Cancellation Fee Paid',
        ]);

        return redirect()->route('customer.orders.show', $order->id)
            ->with('success', 'Order cancelled successfully. Cancellation fee of ₹50 paid via Razorpay.');
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

    public function razorpayGateway(Request $request)
    {
        $amount = floatval($request->get('amount', 50));
        $name = $request->get('name', 'Sip N Bite');
        $description = $request->get('description', 'Online Payment');
        $userName = $request->get('user_name', 'Customer');
        $userEmail = $request->get('user_email', 'customer@sipnbite.com');
        $userPhone = $request->get('user_phone', '9999999999');
        $type = $request->get('type', 'general');
        $orderId = $request->get('order_id', '');
        $razorpayKey = config('services.razorpay.key') ?: 'rzp_test_TXveDStqLBJjaK';

        return view('payment.gateway', compact(
            'amount',
            'name',
            'description',
            'userName',
            'userEmail',
            'userPhone',
            'type',
            'orderId',
            'razorpayKey'
        ));
    }
}
