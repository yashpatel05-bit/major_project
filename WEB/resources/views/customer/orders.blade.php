@extends('layouts.app')

@section('title', 'My Orders — Sip N Bite')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold brand-font mb-4"><i class="fa-solid fa-receipt text-danger me-2"></i> Order History & Tracking</h3>

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-box-open fs-1 text-secondary opacity-50 mb-3"></i>
                <h4 class="fw-bold">No Past Orders Found</h4>
                <p class="text-secondary">Place your first order from our delicious menu!</p>
                <a href="{{ route('customer.menu') }}" class="btn btn-zomato rounded-pill px-4 mt-2">Order Food Now</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Order #{{ $order->order_number }}</h6>
                                <span class="small text-secondary"><i class="fa-solid fa-clock me-1"></i> {{ $order->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <span class="badge bg-{{ $order->order_status === 'Delivered' ? 'success' : ($order->order_status === 'Cancelled' ? 'danger' : 'warning') }} px-3 py-2 text-uppercase rounded-pill">
                                {{ $order->order_status }}
                            </span>
                        </div>

                        <div class="border-top border-bottom py-3 mb-3">
                            @foreach($order->items as $item)
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span><strong class="text-danger">{{ $item->quantity }}x</strong> {{ $item->item_name }}</span>
                                    <span class="fw-semibold">₹{{ number_format($item->subtotal, 0) }}</span>
                                </div>
                            @endforeach
                        </div>

                        @if($order->deliveryPerson)
                            <div class="bg-light p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="small text-secondary d-block">Delivery Staff</span>
                                    <strong class="text-dark"><i class="fa-solid fa-user-ninja text-danger me-1"></i> {{ $order->deliveryPerson->name }}</strong>
                                </div>
                                <a href="tel:{{ $order->deliveryPerson->phone }}" class="btn btn-sm btn-outline-danger rounded-pill"><i class="fa-solid fa-phone me-1"></i> Call Staff</a>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                            <div>
                                <span class="small text-secondary d-block">Total Paid ({{ $order->payment_method }})</span>
                                <span class="fw-bold fs-5 text-danger">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-zomato rounded-pill px-4">Track Order <i class="fa-solid fa-chevron-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
