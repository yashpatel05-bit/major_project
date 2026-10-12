@extends('layouts.app')

@section('title', 'My Profile — Sip N Bite')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Edit Profile Form -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h4 class="fw-bold brand-font mb-3"><i class="fa-solid fa-user-pen text-danger me-2"></i> Account Details</h4>

                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control bg-light" value="{{ $user->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address (Read Only)</label>
                        <input type="email" class="form-control bg-light text-secondary" value="{{ $user->email }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control bg-light" value="{{ $user->phone }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Primary Address</label>
                        <textarea name="address" class="form-control bg-light" rows="2">{{ $user->address }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Update Password (Optional)</label>
                        <input type="password" name="password" class="form-control bg-light">
                    </div>

                    <button type="submit" class="btn btn-zomato rounded-pill px-4 fw-bold">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Address Management -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-location-dot text-danger me-2"></i> Saved Addresses</h5>
                </div>

                @if($addresses->isEmpty())
                    <p class="text-secondary small">No saved addresses yet.</p>
                @else
                    <div class="d-flex flex-column gap-2 mb-4">
                        @foreach($addresses as $addr)
                            <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary text-uppercase mb-1">{{ $addr->title }}</span>
                                    <p class="mb-0 small fw-bold text-dark">{{ $addr->address_line }}</p>
                                    <span class="small text-muted">{{ $addr->city }} - {{ $addr->pincode }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Add New Address Form -->
                <h6 class="fw-bold mb-2 border-top pt-3">Add New Address</h6>
                <form action="{{ route('customer.profile.address') }}" method="POST">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="text" name="title" class="form-control form-control-sm bg-light" placeholder="Title (e.g. Home, Work)" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="pincode" class="form-control form-control-sm bg-light" placeholder="Pincode (e.g. 110016)">
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea name="address_line" class="form-control form-control-sm bg-light" rows="2" placeholder="Full address line" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">+ Add Address</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
