@extends('layouts.admin')

@section('title', 'Order Management — Sip N Bite Admin')
@section('page_header', 'Order Management & Live Dispatch')

@section('content')
<!-- Quick Stats Analytics Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Total Revenue</span>
                    <h3 class="fw-bold mb-0 text-danger">₹{{ number_format($orders->filter(fn($o) => strtolower($o->payment_status ?? '') === 'paid')->sum('total_amount'), 2) }}</h3>
                </div>
                <div class="bg-danger-subtle text-danger rounded-circle p-3 fs-4">💰</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Pending Orders</span>
                    <h3 class="fw-bold mb-0 text-warning">{{ $orders->filter(fn($o) => strtolower($o->order_status ?? '') === 'pending')->count() }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning rounded-circle p-3 fs-4">⏳</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Kitchen Preparing</span>
                    <h3 class="fw-bold mb-0 text-info">{{ $orders->filter(fn($o) => strtolower($o->order_status ?? '') === 'preparing')->count() }}</h3>
                </div>
                <div class="bg-info-subtle text-info rounded-circle p-3 fs-4">🍳</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Out for Delivery</span>
                    <h3 class="fw-bold mb-0 text-primary">{{ $orders->filter(fn($o) => strtolower($o->order_status ?? '') === 'out for delivery')->count() }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle p-3 fs-4">🛵</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm rounded-pill {{ !request('status') || request('status') == 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">All Orders ({{ $orders->count() }})</a>
        <a href="{{ route('admin.orders.index', ['status' => 'Pending']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'Pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('admin.orders.index', ['status' => 'Preparing']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'Preparing' ? 'btn-info text-white' : 'btn-outline-info' }}">Preparing</a>
        <a href="{{ route('admin.orders.index', ['status' => 'Out for Delivery']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'Out for Delivery' ? 'btn-primary' : 'btn-outline-primary' }}">Out for Delivery</a>
        <a href="{{ route('admin.orders.index', ['status' => 'Delivered']) }}" class="btn btn-sm rounded-pill {{ request('status') == 'Delivered' ? 'btn-success' : 'btn-outline-success' }}">Delivered</a>
    </div>
    <button onclick="window.print()" class="btn btn-sm btn-outline-dark rounded-pill px-3">
        <i class="fa-solid fa-print me-1"></i> Print Orders Summary
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Order #</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Current Status</th>
                        <th>Assigned Driver</th>
                        <th class="pe-4 text-end">Update / Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                        @php
                            $status = strtolower($ord->order_status ?? 'pending');
                            $isLocked = in_array($status, ['delivered', 'cancelled']);
                            $isPreparing = $status === 'preparing';
                            $isOutForDelivery = $status === 'out for delivery';

                            $badgeClass = match($status) {
                                'delivered' => 'bg-success',
                                'cancelled' => 'bg-danger',
                                'out for delivery' => 'bg-primary',
                                'preparing' => 'bg-info text-white',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold">#{{ $ord->order_number ?? $ord->id }}</td>
                            <td>
                                <div>
                                    <strong class="text-dark d-block">{{ $ord->user ? $ord->user->name : ($ord->guest_name ?? 'Customer') }}</strong>
                                    <span class="small text-secondary">{{ $ord->user ? $ord->user->phone : ($ord->guest_phone ?? '') }}</span>
                                </div>
                            </td>
                            <td class="fw-bold text-danger">₹{{ number_format($ord->total_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border me-1">{{ $ord->payment_method ?? 'COD' }}</span>
                                <span class="badge bg-{{ strtolower($ord->payment_status ?? '') === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($ord->payment_status ?? 'unpaid') }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }} text-uppercase px-3 py-1">
                                    {{ $ord->order_status }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.orders.update-status', $ord->id) }}" method="POST" id="dp_form_{{ $ord->id }}">
                                    @csrf
                                    <input type="hidden" name="order_status" value="{{ $ord->order_status }}">
                                    <select name="delivery_person_id" class="form-select form-select-sm rounded-pill border-secondary" style="min-width: 140px;" onchange="this.form.submit()" {{ $isLocked ? 'disabled' : '' }}>
                                        <option value="">Unassigned</option>
                                        @foreach($deliveryPersons ?? [] as $dp)
                                            <option value="{{ $dp->id }}" {{ $ord->delivery_person_id == $dp->id ? 'selected' : '' }}>
                                                {{ $dp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.orders.update-status', $ord->id) }}" method="POST" class="d-inline-flex gap-1 align-items-center">
                                    @csrf
                                    @if($ord->delivery_person_id)
                                        <input type="hidden" name="delivery_person_id" value="{{ $ord->delivery_person_id }}">
                                    @endif
                                    <select name="order_status" class="form-select form-select-sm rounded-pill border-danger" style="width: 165px;" onchange="this.form.submit()" {{ $isLocked ? 'disabled' : '' }}>
                                        <option value="Pending" {{ $status == 'pending' ? 'selected' : '' }} {{ ($isPreparing || $isOutForDelivery || $isLocked) ? 'disabled' : '' }}>
                                            Pending {{ ($isPreparing || $isOutForDelivery) ? '🔒' : '' }}
                                        </option>
                                        <option value="Preparing" {{ $status == 'preparing' ? 'selected' : '' }} {{ ($isOutForDelivery || $isLocked) ? 'disabled' : '' }}>
                                            Preparing {{ $isOutForDelivery ? '🔒' : '' }}
                                        </option>
                                        <option value="Out for Delivery" {{ $status == 'out for delivery' ? 'selected' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                                            Out for Delivery
                                        </option>
                                        <option value="Delivered" {{ $status == 'delivered' ? 'selected' : '' }}>
                                            Delivered
                                        </option>
                                        <option value="Cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>
                                            Cancelled
                                        </option>
                                    </select>
                                </form>
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-light text-danger rounded-circle ms-1" title="View Details & Assign"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('admin.invoices.show', $ord->id) }}" class="btn btn-sm btn-light text-dark rounded-circle" target="_blank" title="Invoice"><i class="fa-solid fa-file-invoice"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-basket-shopping fs-1 d-block mb-2 text-muted"></i>
                                <h6>No orders found matching your criteria</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
