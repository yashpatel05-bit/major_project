<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\DeliveryPerson;

class OrderApiController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required',
            'items.*.item_name' => 'required',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:COD,Razorpay',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $tax = $subtotal * 0.05;
        $deliveryFee = 40.00;
        $total = $subtotal + $tax + $deliveryFee;

        // Delivery partner must be assigned manually by Admin (do not auto-assign)
        $order = Order::create([
            'order_number' => 'SNB-' . strtoupper(substr(uniqid(), -6)),
            'user_id' => $request->user_id,
            'delivery_person_id' => null,
            'address_id' => $request->address_id ?: null,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_method === 'Razorpay' ? 'paid' : 'pending',
            'razorpay_order_id' => $request->razorpay_order_id ?: null,
            'razorpay_payment_id' => $request->razorpay_payment_id ?: null,
            'order_status' => 'Pending',
            'notes' => $request->notes
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'item_name' => $item['item_name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        }

        if ($request->payment_method === 'Razorpay') {
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $request->user_id,
                'razorpay_payment_id' => $request->razorpay_payment_id ?: 'rzp_test_' . uniqid(),
                'razorpay_order_id' => $request->razorpay_order_id ?: 'order_' . uniqid(),
                'amount' => $total,
                'payment_method' => 'Razorpay',
                'status' => 'Success'
            ]);
        }

        $order->load(['items', 'deliveryPerson', 'address']);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'razorpay_payment_id' => 'required',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->payment_status = 'paid';
        $order->razorpay_payment_id = $request->razorpay_payment_id;
        $order->save();

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id ?: 'order_' . uniqid(),
            'amount' => $order->total_amount,
            'payment_method' => 'Razorpay',
            'status' => 'Success'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully'
        ]);
    }

    public function getUserOrders($userId)
    {
        $orders = Order::with(['items', 'deliveryPerson', 'address', 'feedback'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function getOrderDetails($id)
    {
        $order = Order::with(['items', 'deliveryPerson', 'address', 'payment', 'feedback'])->find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
        ]);

        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        if ($request->filled('user_id') && $order->user_id && (string)$order->user_id !== (string)$request->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: This order belongs to another customer account.'
            ], 403);
        }

        if (in_array($order->order_status, ['Delivered', 'Cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => "Order cannot be cancelled as it is already {$order->order_status}."
            ], 400);
        }

        // Release delivery partner if assigned
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
        $order->cancellation_reason = $request->cancellation_reason ?: 'Cancelled by customer on tracking screen';
        $order->cancelled_at = now();
        $order->save();

        Payment::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id ?: 'cancel_' . uniqid(),
            'amount' => 50.00,
            'payment_method' => 'Razorpay',
            'status' => 'Cancellation Fee Paid',
        ]);

        $order->load(['items', 'deliveryPerson', 'address', 'payment', 'feedback']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully. Cancellation fee of ₹50 paid.',
            'data' => $order
        ]);
    }
}
