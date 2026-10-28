@extends('layouts.app')

@section('title', 'Track Order #' . $order->order_number . ' — Sip N Bite')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2 fs-5 align-middle"></i>
                    <strong>{{ session('success') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5 align-middle"></i>
                    <strong>{{ session('error') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="bg-dark text-white p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold brand-font mb-1">Order #{{ $order->order_number }}</h4>
                        <span class="small text-secondary"><i class="fa-solid fa-clock me-1"></i> {{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <span class="badge bg-{{ $order->order_status === 'Cancelled' ? 'danger' : ($order->order_status === 'Delivered' ? 'success' : 'warning') }} fs-6 px-3 py-2 rounded-pill text-uppercase">
                        {{ $order->order_status }}
                    </span>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Progress Timeline Bar -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-uppercase text-secondary">Live Order Lifecycle</h6>
                            @if(!in_array($order->order_status, ['Delivered', 'Cancelled']))
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#cancelModalStep1">
                                    <i class="fa-solid fa-ban me-1"></i> Cancel Order
                                </button>
                            @endif
                        </div>

                        @php
                            $statuses = ['Pending', 'Preparing', 'Out for Delivery', 'Delivered'];
                            $currentIdx = array_search($order->order_status, $statuses);
                            if ($currentIdx === false && $order->order_status === 'Cancelled') $currentIdx = -1;
                        @endphp

                        @if($order->order_status === 'Cancelled')
                            <div class="alert alert-danger rounded-4 p-4 text-center mb-0 border-0 shadow-sm" style="background-color: #FEF2F2;">
                                <div class="fs-1 text-danger mb-2"><i class="fa-solid fa-ban"></i></div>
                                <h5 class="fw-bold text-danger mb-1">This order was cancelled.</h5>
                                @if($order->cancellation_fee > 0)
                                    <p class="mb-0 text-dark small fw-semibold">
                                        Cancellation fee of <span class="badge bg-danger fs-6 px-2 py-1">₹{{ number_format($order->cancellation_fee, 2) }}</span> was paid online.
                                        @if($order->cancellation_payment_id)
                                            <br><span class="text-secondary">Razorpay Payment ID: <code>{{ $order->cancellation_payment_id }}</code></span>
                                        @endif
                                    </p>
                                @endif
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
                    @if($order->deliveryPerson && $order->order_status !== 'Cancelled')
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

                        @if($order->cancellation_fee > 0)
                            <div class="d-flex justify-content-between mb-1 small text-danger fw-bold">
                                <span>Cancellation Fee (Paid Online)</span>
                                <span>+ ₹{{ number_format($order->cancellation_fee, 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                            <span class="fw-bold fs-5">Order Payment ({{ $order->payment_method }})</span>
                            <span class="fw-bold fs-4 text-danger">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <!-- Cancel Order Button Footer Action -->
                    @if(!in_array($order->order_status, ['Delivered', 'Cancelled']))
                        <div class="mt-4 pt-3 border-top text-center">
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#cancelModalStep1">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i> Cancel Order
                            </button>
                        </div>
                    @endif
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

<!-- ========================================================
     MODAL STEP 1: Wastage of Food Warning
     ======================================================== -->
<div class="modal fade" id="cancelModalStep1" tabindex="-1" aria-labelledby="cancelModalStep1Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-danger-subtle text-danger py-3 px-4">
                <h5 class="modal-title fw-bold" id="cancelModalStep1Label">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Order Cancellation Notice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="my-2">
                    <span class="display-3 text-warning">🍲</span>
                </div>
                <div class="alert alert-warning border-0 rounded-4 p-3 my-3">
                    <h5 class="fw-bold text-danger mb-1">
                        "order cancellation is a wastage of food."
                    </h5>
                </div>
                <p class="text-secondary small mb-0">
                    Our chef and kitchen team may have already prepared fresh ingredients for your meal. Are you sure you wish to proceed?
                </p>
            </div>
            <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" data-bs-dismiss="modal">
                    <i class="fa-solid fa-check me-1"></i> 1. No Cancel
                </button>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="proceedToCancelStep2()">
                    2. Cancel Anyway <i class="fa-solid fa-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL STEP 2: Cancellation Fees Required (50 Rs Online Only)
     ======================================================== -->
<div class="modal fade" id="cancelModalStep2" tabindex="-1" aria-labelledby="cancelModalStep2Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-dark text-white py-3 px-4">
                <h5 class="modal-title fw-bold" id="cancelModalStep2Label">
                    <i class="fa-solid fa-credit-card text-danger me-2"></i> Cancellation Fee
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="alert alert-danger border-0 rounded-4 p-3 mb-3" style="background-color: #FEE2E2;">
                    <h5 class="fw-bold text-danger mb-1">
                        "cancellation fees you have to pay."
                    </h5>
                    <div class="display-5 fw-bold text-dark my-2">₹50</div>
                    <span class="badge bg-danger rounded-pill px-3 py-2 text-uppercase fs-6">
                        fees :- 50 rs. online payment mode only.
                    </span>
                </div>
                <p class="text-secondary small mb-0">
                    To finalize cancellation and compensate for food preparation, an online payment of <strong>₹50</strong> is mandatory via Razorpay (UPI, Google Pay, PhonePe, Cards, NetBanking).
                </p>
            </div>
            <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold" data-bs-dismiss="modal">
                    Keep Order
                </button>
                <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnPayCancellationFee" onclick="openRazorpayCancellation()">
                    <i class="fa-solid fa-bolt me-1"></i> Pay ₹50 Online & Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Form for Cancellation Submission -->
<form id="cancelOrderForm" action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" style="display: none;" data-no-validate="true">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="cancel_razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="cancel_razorpay_order_id">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function proceedToCancelStep2() {
        var step1El = document.getElementById('cancelModalStep1');
        var step1Modal = bootstrap.Modal.getInstance(step1El);
        if (step1Modal) {
            step1Modal.hide();
        }

        setTimeout(function() {
            var step2El = document.getElementById('cancelModalStep2');
            var step2Modal = new bootstrap.Modal(step2El);
            step2Modal.show();
        }, 350);
    }

    function openRazorpayCancellation() {
        var step2El = document.getElementById('cancelModalStep2');
        var step2Modal = bootstrap.Modal.getInstance(step2El);
        if (step2Modal) {
            step2Modal.hide();
        }

        var btn = document.getElementById('btnPayCancellationFee');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting Gateway...';
        }

        var options = {
            "key": "{{ config('services.razorpay.key') }}",
            "amount": 5000, // 50 rs in paise
            "currency": "INR",
            "name": "Sip N Bite",
            "description": "Order Cancellation Fee #{{ $order->order_number }}",
            "image": "https://cdn-icons-png.flaticon.com/512/3170/3170733.png",
            "handler": function (response) {
                document.getElementById('cancel_razorpay_payment_id').value = response.razorpay_payment_id;
                if (response.razorpay_order_id) {
                    document.getElementById('cancel_razorpay_order_id').value = response.razorpay_order_id;
                }
                document.getElementById('cancelOrderForm').submit();
            },
            "prefill": {
                "name": "{{ Auth::user()->name }}",
                "email": "{{ Auth::user()->email }}",
                "contact": "{{ Auth::user()->phone ?? '' }}"
            },
            "theme": {
                "color": "#E23744"
            },
            "modal": {
                "ondismiss": function() {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> Pay ₹50 Online & Cancel';
                    }
                    alert("Cancellation fee payment was not completed. Order remains active.");
                }
            }
        };

        var rzpCancel = new Razorpay(options);
        rzpCancel.on('payment.failed', function (response) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-bolt me-1"></i> Pay ₹50 Online & Cancel';
            }
            alert("Payment Failed: " + (response.error.description || 'Could not process cancellation fee.'));
        });
        rzpCancel.open();
    }
</script>
@endsection
