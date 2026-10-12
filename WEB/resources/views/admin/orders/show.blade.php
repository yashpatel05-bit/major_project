@extends('layouts.admin')

@section('title', 'Order Details #' . $order->order_number . ' — Sip N Bite Admin')
@section('page_header', 'Order Details #' . $order->order_number)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Purchased Menu Items</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="fw-bold text-dark">{{ $item->item_name }}</td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end fw-bold text-danger">₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between mb-1 text-secondary">
                    <span>Items Subtotal</span>
                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-secondary">
                    <span>Tax (5%)</span>
                    <span>₹{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-secondary">
                    <span>Delivery Fee</span>
                    <span>₹{{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2">
                    <span class="fw-bold fs-5">Total Paid</span>
                    <span class="fw-bold fs-4 text-danger">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Feedback if present -->
        @if($order->feedback)
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-2"><i class="fa-solid fa-star text-warning me-2"></i> Customer Review</h5>
                <p class="mb-1"><strong>Rating:</strong> {{ $order->feedback->rating }} / 5 Stars</p>
                <p class="text-secondary mb-0">"{{ $order->feedback->comment }}"</p>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Update Order Status & Assign Staff -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Manage Order Status</h5>
            
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Order Status</label>
                    <select name="order_status" class="form-select bg-light" required>
                        <option value="Pending" {{ $order->order_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Preparing" {{ $order->order_status == 'Preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="Out for Delivery" {{ $order->order_status == 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="Delivered" {{ $order->order_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="Cancelled" {{ $order->order_status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Assign Delivery Personnel</label>
                    <select name="delivery_person_id" class="form-select bg-light">
                        <option value="">Unassigned</option>
                        @foreach($deliveryPersons as $dp)
                            <option value="{{ $dp->id }}" {{ $order->delivery_person_id == $dp->id ? 'selected' : '' }}>
                                {{ $dp->name }} ({{ $dp->status }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Update Order</button>
            </form>
        </div>

        <!-- Customer & Delivery Address -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">Customer & Address</h5>
            
            <p class="mb-1"><strong>Name:</strong> {{ $order->user ? $order->user->name : 'N/A' }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ $order->user ? $order->user->email : 'N/A' }}</p>
            <p class="mb-3"><strong>Phone:</strong> {{ $order->user ? $order->user->phone : 'N/A' }}</p>

            <h6 class="fw-bold mb-1">Delivery Address</h6>
            <p class="text-secondary small mb-0">{{ $order->address ? $order->address->address_line . ', ' . $order->address->city : 'Self Pickup / Counter' }}</p>
        </div>
    </div>
</div>
@endsection
