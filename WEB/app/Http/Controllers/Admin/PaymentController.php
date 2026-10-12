<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['order', 'user'])->latest()->get();
        $codOrders = Order::where('payment_method', 'COD')->with('user')->latest()->get();
        return view('admin.payments.index', compact('payments', 'codOrders'));
    }
}
