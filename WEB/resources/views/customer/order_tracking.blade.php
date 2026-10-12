@extends('layouts.app')

@section('title', 'Track Order #' . $order->order_number . ' — Sip N Bite')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="bg-dark text-white p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold brand-font mb-1">Order #{{ $order->order_number }}</h4>
                        <span class="small text-secondary"><i class="fa-solid fa-clock me-1"></i> {{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill text-uppercase">{{ $order->order_status }}</span>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Progress Timeline Bar -->
                    <div class="mb-5">
                        <h6 class="fw-bold mb-3 text-uppercase text-secondary">Live Order Lifecycle</h6>
                        @php
                            $statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered'];
                            $currentIdx = array_search($order->order_status, $statuses);
                            if ($currentIdx === false && $order->order_status === 'Cancelled') $currentIdx = -1;
                        @endphp

                        @if($order->order_status === 'Cancelled')
                            <div class="alert alert-danger rounded-4 py-3 text-center fw-bold mb-0">
                                <i class="fa-solid fa-ban me-2"></i> This order was cancelled.
                            </div>
                        @else
                            <div class="d-flex justify-content-between position-relative py-3">
                                @foreach($statuses as $index => $st)
                                    <div class="text-center z-1">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold text-white shadow-sm"
                                             style="width: 44px; height: 44px; background-color: {{ $index <= $currentIdx ? '#E23744' : '#e5e7eb' }}; border: 3px solid {{ $index <= $currentIdx ? '#ffffff' : '#f3f4f6' }};">
                                            @if($index < $currentIdx)
                                                <i class="fa-solid fa-check"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>
                                        <span class="small fw-semibold {{ $index <= $currentIdx ? 'text-danger' : 'text-secondary' }}">{{ $st }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Delivery Person Info -->
                    @if($order->deliveryPerson)
                        <div class="card border-0 bg-light rounded-4 p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-danger text-white rounded-circle p-3 fs-4">
                                        <i class="fa-solid fa-motorcycle"></i>
                                    </div>
                                    <div>
                                        <span class="small text-secondary d-block">Assigned Delivery Executive</span>
                                        <h6 class="fw-bold mb-0">{{ $order->deliveryPerson->name }}</h6>
                                        <span class="small text-muted">{{ $order->deliveryPerson->vehicle_number ?: 'Bike Delivery' }}</span>
                                    </div>
                                </div>
                                <a href="tel:{{ $order->deliveryPerson->phone }}" class="btn btn-danger rounded-pill px-4 fw-bold"><i class="fa-solid fa-phone me-2"></i> {{ $order->deliveryPerson->phone }}</a>
                            </div>
                        </div>
                    @endif

                    <!-- Ordered Items & Payment -->
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Order Items</h6>
                    <div class="mb-4">
                        @foreach($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span><strong class="text-danger">{{ $item->quantity }}x</strong> {{ $item->item_name }}</span>
                                <span class="fw-semibold">₹{{ number_format($item->subtotal, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-1 small text-secondary">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1 small text-secondary">
                            <span>Taxes & Fees</span>
                            <span>₹{{ number_format($order->tax + $order->delivery_fee, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                            <span class="fw-bold fs-5">Total Paid ({{ $order->payment_method }})</span>
                            <span class="fw-bold fs-4 text-danger">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback & Rating Form -->
            @if($order->order_status === 'Delivered')
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-star text-warning me-2"></i> Rate & Review Order</h5>
                    <p class="small text-secondary mb-3">How was your food and delivery experience?</p>

                    @if($order->feedback)
                        <div class="alert alert-success rounded-3 mb-0">
                            <strong>Your Rating:</strong> {{ $order->feedback->rating }} / 5 Stars
                            <p class="mb-0 small text-dark mt-1">"{{ $order->feedback->comment }}"</p>
                        </div>
                    @else
                        <form action="{{ route('customer.feedback.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rating (1 to 5 Stars)</label>
                                <select name="rating" class="form-select bg-light" required>
                                    <option value="5" selected>⭐⭐⭐⭐⭐ (5/5) Excellent Food & Delivery</option>
                                    <option value="4">⭐⭐⭐⭐ (4/5) Very Good</option>
                                    <option value="3">⭐⭐⭐ (3/5) Average</option>
                                    <option value="2">⭐⭐ (2/5) Poor</option>
                                    <option value="1">⭐ (1/5) Terrible</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Comments / Review</label>
                                <textarea name="comment" class="form-control bg-light" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-zomato rounded-pill px-4 fw-bold">Submit Feedback</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
