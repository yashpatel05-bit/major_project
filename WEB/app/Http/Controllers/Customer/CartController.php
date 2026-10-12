<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $tax = $subtotal * 0.05;
        $deliveryFee = $subtotal > 0 ? 40 : 0;
        $total = $subtotal + $tax + $deliveryFee;

        return view('customer.cart', compact('cart', 'subtotal', 'tax', 'deliveryFee', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = MenuItem::findOrFail($request->menu_item_id);
        $cart = session()->get('cart', []);

        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $request->quantity;
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image' => $item->image,
                'is_veg' => $item->is_veg,
                'quantity' => $request->quantity
            ];
        }

        session()->put('cart', $cart);
        return back()->with('success', $item->name . ' added to cart!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->menu_item_id])) {
            if ($request->quantity > 0) {
                $cart[$request->menu_item_id]['quantity'] = $request->quantity;
            } else {
                unset($cart[$request->menu_item_id]);
            }
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Item removed from cart.');
    }
}
