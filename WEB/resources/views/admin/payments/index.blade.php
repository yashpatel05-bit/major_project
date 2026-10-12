@extends('layouts.admin')

@section('title', 'Payment Transactions — Sip N Bite Admin')
@section('page_header', 'Payment Tracking')

@section('content')
<div class="row g-4">
    <!-- Razorpay Online Transactions -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bolt text-warning me-2"></i> Razorpay Online Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Razorpay Payment ID</th>
                                <th>Order #</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $p)
                                <tr>
                                    <td class="ps-4 fw-mono text-dark">{{ $p->razorpay_payment_id }}</td>
                                    <td class="fw-bold">#{{ $p->order ? $p->order->order_number : 'N/A' }}</td>
                                    <td class="fw-bold text-success">₹{{ number_format($p->amount, 2) }}</td>
                                    <td><span class="badge bg-success text-uppercase">SUCCESS</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-secondary">No Razorpay online transactions recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Cash on Delivery Orders -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-money-bill-wave text-success me-2"></i> Cash on Delivery Orders</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($codOrders as $cod)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $cod->order_number }}</td>
                                    <td>{{ $cod->user ? $cod->user->name : 'Customer' }}</td>
                                    <td class="fw-bold text-danger">₹{{ number_format($cod->total_amount, 2) }}</td>
                                    <td><span class="badge bg-{{ $cod->payment_status === 'paid' ? 'success' : 'warning' }} text-uppercase">{{ $cod->payment_status }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-secondary">No COD orders found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
