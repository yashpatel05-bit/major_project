<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class InvoiceController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['user', 'items.menuItem', 'address', 'payment'])->findOrFail($id);
        return view('admin.invoices.show', compact('order'));
    }
}
