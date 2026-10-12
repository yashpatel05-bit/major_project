@extends('layouts.admin')

@section('title', 'Table Bookings Management — Sip N Bite Admin')
@section('page_header', 'Table Bookings Management')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Customer</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Table</th>
                        <th>Special Request</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Update Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark d-block">{{ $b->user ? $b->user->name : 'Customer' }}</strong>
                                <span class="small text-secondary">{{ $b->user ? $b->user->phone : '' }}</span>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-calendar me-1 text-danger"></i> {{ $b->booking_date }}</div>
                                <span class="small text-secondary"><i class="fa-solid fa-clock me-1"></i> {{ $b->booking_time }}</span>
                            </td>
                            <td class="fw-bold">{{ $b->guests_count }} Guests</td>
                            <td>
                                <span class="badge bg-danger rounded-pill px-3 py-1">{{ $b->table ? $b->table->table_number : 'Unassigned' }}</span>
                            </td>
                            <td class="small text-secondary" style="max-width: 200px;">{{ $b->special_request ?: 'None' }}</td>
                            <td>
                                <span class="badge bg-{{ $b->status === 'confirmed' ? 'success' : ($b->status === 'cancelled' ? 'danger' : 'warning') }} text-uppercase px-3 py-1">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.bookings.update', $b->id) }}" method="POST" class="d-inline-flex gap-2">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                                        <option value="pending" {{ $b->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $b->status == 'confirmed' ? 'selected' : '' }}>Confirm</option>
                                        <option value="completed" {{ $b->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $b->status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
