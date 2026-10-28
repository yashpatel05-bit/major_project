@extends('layouts.app')

@section('title', 'Checkout — Sip N Bite')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold brand-font mb-4"><i class="fa-solid fa-credit-card text-danger me-2"></i> Checkout & Payment</h3>

    <form action="{{ route('customer.checkout.place') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="row g-4">
            <div class="col-lg-7">
                <!-- Delivery Address Selection -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-location-dot text-danger me-2"></i> Delivery Address</h5>

                    @if($addresses->isEmpty())
                        <div class="alert alert-warning rounded-3 mb-3">
                            No saved addresses found. Please enter your delivery address in your <a href="{{ route('customer.profile') }}" class="fw-bold">Profile</a> first or set default.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($addresses as $addr)
                                <div class="col-md-6">
                                    <div class="form-check card-select p-3 border rounded-3 h-100">
                                        <input class="form-check-input" type="radio" name="address_id" id="addr_{{ $addr->id }}" value="{{ $addr->id }}" {{ $loop->first ? 'checked' : '' }} required>
                                        <label class="form-check-label w-100 cursor-pointer" for="addr_{{ $addr->id }}">
                                            <span class="badge bg-secondary text-uppercase mb-1">{{ $addr->title }}</span>
                                            <p class="small mb-0 text-dark fw-semibold">{{ $addr->address_line }}</p>
                                            <span class="small text-secondary">{{ $addr->city }} - {{ $addr->pincode }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Payment Method -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-wallet text-danger me-2"></i> Payment Method</h5>

                    <div class="form-check p-3 border rounded-3 mb-3">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="COD" checked>
                        <label class="form-check-label fw-bold cursor-pointer" for="pay_cod">
                            <i class="fa-solid fa-money-bill-wave text-success me-2 fs-5"></i> Cash on Delivery (COD)
                            <p class="small text-secondary fw-normal mb-0">Pay in cash when your delicious food arrives.</p>
                        </label>
                    </div>

                    <div class="form-check p-3 border rounded-3">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay_razorpay" value="Razorpay">
                        <label class="form-check-label fw-bold cursor-pointer" for="pay_razorpay">
                            <i class="fa-solid fa-bolt text-warning me-2 fs-5"></i> Online Payment
                            <p class="small text-secondary fw-normal mb-0">UPI, Cards, NetBanking</p>
                        </label>
                    </div>
                </div>

                <!-- Hidden Razorpay Fields -->
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">

                <!-- Order Notes -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-note-sticky text-danger me-2"></i> Special Delivery Notes</h5>
                    <textarea name="notes" class="form-control bg-light" rows="2"></textarea>
                </div>
            </div>

            <!-- Summary Column -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Order Summary</h5>

                    <div class="mb-3 max-vh-40 overflow-auto">
                        @foreach($cart as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 small">
                                <div>
                                    <span class="fw-bold">{{ $item['quantity'] }}x</span> {{ $item['name'] }}
                                </div>
                                <span class="fw-semibold">₹{{ number_format($item['price'] * $item['quantity'], 0) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Taxes & GST (5%)</span>
                        <span>₹{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-secondary">
                        <span>Delivery Fee</span>
                        <span>₹{{ number_format($deliveryFee, 2) }}</span>
                    </div>

                    <div class="d-flex justify-content-between my-3 pt-2 border-top">
                        <span class="fw-bold fs-5">Grand Total</span>
                        <span class="fw-bold fs-4 text-danger">₹{{ number_format($total, 2) }}</span>
                    </div>

                    <button type="submit" id="btnPlaceOrder" class="btn btn-zomato w-100 py-3 rounded-3 fw-bold fs-6 mt-2">Place Order Now <i class="fa-solid fa-check-circle ms-2"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var checkoutForm = document.getElementById('checkoutForm');
        
        checkoutForm.addEventListener('submit', function(e) {
            var selectedAddr = document.querySelector('input[name="address_id"]:checked');
            if (!selectedAddr) {
                e.preventDefault();
                alert('Please enter this field: Delivery Address. Please select or add an address to continue.');
                return false;
            }

            var selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            var paymentMethod = selectedPayment ? selectedPayment.value : 'COD';
            
            if (paymentMethod === 'Razorpay') {
                var paymentId = document.getElementById('razorpay_payment_id').value;
                if (!paymentId) {
                    e.preventDefault();

                    var options = {
                        "key": "{{ config('services.razorpay.key') }}",
                        "amount": Math.round({{ $total }} * 100),
                        "currency": "INR",
                        "name": "Sip N Bite",
                        "description": "Food Order Payment",
                        "image": "https://cdn-icons-png.flaticon.com/512/3170/3170733.png",
                        "handler": function (response) {
                            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                            if (response.razorpay_order_id) {
                                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                            }
                            checkoutForm.submit();
                        },
                        "prefill": {
                            "name": "{{ Auth::user()->name }}",
                            "email": "{{ Auth::user()->email }}",
                            "contact": "{{ Auth::user()->phone ?? '' }}"
                        },
                        "theme": {
                            "color": "#dc3545"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.on('payment.failed', function (response) {
                        alert("Payment Failed: " + response.error.description);
                    });
                    rzp1.open();
                }
            }
        });
    });
</script>
@endsection
