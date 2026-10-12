@extends('layouts.admin')

@section('title', 'Admin Dashboard — Sip N Bite')
@section('page_header', 'Dashboard Overview')

@section('content')
<!-- Analytics Stats Grid -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary fw-semibold">Total Revenue</span>
                <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="fa-solid fa-indian-rupee-sign fs-5"></i></span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">₹{{ number_format($totalRevenue, 2) }}</h3>
            <span class="small text-success"><i class="fa-solid fa-arrow-up-right me-1"></i> Live earnings</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary fw-semibold">Total Orders</span>
                <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="fa-solid fa-bag-shopping fs-5"></i></span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $totalOrders }}</h3>
            <span class="small text-secondary">All-time customer orders</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary fw-semibold">Active Bookings</span>
                <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="fa-solid fa-chair fs-5"></i></span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $activeBookings }}</h3>
            <span class="small text-primary">Pending & confirmed tables</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-secondary fw-semibold">Active Delivery Staff</span>
                <span class="badge bg-warning-subtle text-warning p-2 rounded-circle"><i class="fa-solid fa-motorcycle fs-5"></i></span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $activeDeliveryStaff }}</h3>
            <span class="small text-warning">On live delivery duty</span>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Customer Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-danger rounded-pill">View All Orders</a>
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
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $ord)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $ord->order_number }}</td>
                                    <td>{{ $ord->user ? $ord->user->name : 'Guest' }}</td>
                                    <td class="fw-semibold">₹{{ number_format($ord->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ord->order_status === 'Delivered' ? 'success' : ($ord->order_status === 'Cancelled' ? 'danger' : 'warning') }} rounded-pill px-3 py-1 text-uppercase">
                                            {{ $ord->order_status }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-light text-danger rounded-circle"><i class="fa-solid fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-secondary">No orders found yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Table Reservations Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3">Recent Table Reservations</h5>
            <div class="d-flex flex-column gap-3">
                @forelse($recentBookings as $b)
                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-dark d-block">{{ $b->user ? $b->user->name : 'Customer' }}</strong>
                            <span class="small text-secondary"><i class="fa-solid fa-calendar me-1"></i> {{ $b->booking_date }} at {{ $b->booking_time }}</span>
                            <span class="badge bg-secondary ms-1">{{ $b->guests_count }} Guests</span>
                        </div>
                        <span class="badge bg-{{ $b->status === 'confirmed' ? 'success' : 'warning' }} text-uppercase">{{ $b->status }}</span>
                    </div>
                @empty
                    <p class="text-secondary small mb-0">No active bookings right now.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill mt-3">Manage Reservations</a>
        </div>
    </div>
</div>
@endsection
