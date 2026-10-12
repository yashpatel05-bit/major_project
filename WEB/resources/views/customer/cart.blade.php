@extends('layouts.app')

@section('title', 'Your Cart — Sip N Bite')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold brand-font mb-4"><i class="fa-solid fa-cart-shopping text-danger me-2"></i> Shopping Cart</h3>

    @if(empty($cart))
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-basket-shopping display-1 text-secondary opacity-50 mb-3"></i>
                <h4 class="fw-bold">Your Cart is Empty</h4>
                <p class="text-secondary">Explore our mouth-watering menu and add your favourite dishes!</p>
                <a href="{{ route('customer.menu') }}" class="btn btn-zomato rounded-pill px-4 mt-2">Browse Menu</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $item)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $item['image'] }}" class="rounded-3" style="width: 54px; height: 54px; object-fit: cover;" alt="{{ $item['name'] }}">
                                                    <div>
                                                        <span class="{{ !empty($item['is_veg']) ? 'veg-badge' : 'nonveg-badge' }} me-1"></span>
                                                        <span class="fw-bold text-dark">{{ $item['name'] }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-semibold">₹{{ number_format($item['price'], 0) }}</td>
                                            <td>
                                                <form action="{{ route('customer.cart.update') }}" method="POST" class="d-flex align-items-center gap-1">
                                                    @csrf
                                                    <input type="hidden" name="menu_item_id" value="{{ $id }}">
                                                    <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="btn btn-sm btn-outline-secondary rounded-circle px-2 py-0">-</button>
                                                    <span class="fw-bold px-2">{{ $item['quantity'] }}</span>
                                                    <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="btn btn-sm btn-outline-secondary rounded-circle px-2 py-0">+</button>
                                                </form>
                                            </td>
                                            <td class="fw-bold text-danger">₹{{ number_format($item['price'] * $item['quantity'], 0) }}</td>
                                            <td class="pe-4 text-end">
                                                <a href="{{ route('customer.cart.remove', $id) }}" class="btn btn-sm btn-light text-danger rounded-circle"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <a href="{{ route('customer.menu') }}" class="btn btn-outline-secondary rounded-pill"><i class="fa-solid fa-arrow-left me-2"></i> Add More Items</a>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Bill Details</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Item Subtotal</span>
                        <span class="fw-semibold">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">GST & Taxes (5%)</span>
                        <span class="fw-semibold">₹{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-secondary">Delivery Charge</span>
                        <span class="fw-semibold">₹{{ number_format($deliveryFee, 2) }}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">To Pay</span>
                        <span class="fw-bold fs-4 text-danger">₹{{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('customer.checkout') }}" class="btn btn-zomato w-100 py-3 rounded-3 fw-bold fs-6">Proceed to Checkout <i class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
