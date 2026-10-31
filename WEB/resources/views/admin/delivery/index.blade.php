@extends('layouts.admin')

@section('title', 'Delivery Personnel — Sip N Bite Admin')
@section('page_header', 'Delivery Personnel Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Delivery Staff Members ({{ count($deliveryPersons) }})</h5>
    <button class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addStaffModal">+ Add Delivery Executive</button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Phone</th>
                        <th>Vehicle Number</th>
                        <th>Live Duty Status</th>
                        <th>Active Deliveries</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryPersons as $dp)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark d-block"><i class="fa-solid fa-user-ninja text-danger me-2"></i> {{ $dp->name }}</strong>
                                <span class="small text-secondary">{{ $dp->email }}</span>
                            </td>
                            <td><a href="tel:{{ $dp->phone }}" class="text-decoration-none fw-semibold text-dark"><i class="fa-solid fa-phone text-danger me-1"></i> {{ $dp->phone }}</a></td>
                            <td><span class="badge bg-light text-dark border px-3 py-1">{{ $dp->vehicle_number ?: 'Bike' }}</span></td>
                            <td>
                                <span class="badge bg-{{ $dp->status === 'available' ? 'success' : ($dp->status === 'on_delivery' ? 'warning' : 'secondary') }} text-uppercase px-3 py-1">
                                    {{ $dp->status }}
                                </span>
                            </td>
                            <td><span class="badge bg-secondary rounded-pill px-3 py-1">{{ $dp->orders_count }} Active</span></td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.delivery.destroy', $dp->id) }}" class="btn btn-sm btn-light text-danger rounded-circle" onclick="return confirm('Remove delivery person?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Delivery Staff -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-header-title fw-bold mb-0">Add Delivery Executive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.delivery.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control bg-light" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control bg-light" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address (Optional)</label>
                        <input type="email" name="email" class="form-control bg-light">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Vehicle Plate Number</label>
                        <input type="text" name="vehicle_number" class="form-control bg-light">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Save Executive</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
