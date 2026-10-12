@extends('layouts.admin')

@section('title', 'Customer Management — Sip N Bite Admin')
@section('page_header', 'Customer Management')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Customer Name</th>
                        <th>Email Address</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Account Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $c)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <i class="fa-solid fa-circle-user me-2 text-danger fs-5"></i> {{ $c->name }}
                            </td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->phone ?: 'N/A' }}</td>
                            <td><span class="badge bg-secondary rounded-pill px-3 py-1">{{ $c->orders_count }} Orders</span></td>
                            <td>
                                <span class="badge bg-{{ $c->status === 'active' ? 'success' : 'danger' }} text-uppercase">{{ $c->status }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.customers.toggle', $c->id) }}" class="btn btn-sm btn-outline-{{ $c->status === 'active' ? 'danger' : 'success' }} rounded-pill px-3 fw-bold">
                                    {{ $c->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
