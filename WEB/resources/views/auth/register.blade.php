@extends('layouts.app')

@section('title', 'Sign Up — Sip N Bite')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-danger text-white text-center py-4">
                    <h3 class="fw-bold mb-1"><i class="fa-solid fa-utensils"></i> Join Sip N Bite</h3>
                    <p class="small mb-0 opacity-75">Create an account for quick food delivery & table bookings</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 mb-4">
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control bg-light" required value="{{ old('name') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control bg-light" required value="{{ old('email') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-light" value="{{ old('phone') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Delivery Address</label>
                            <textarea name="address" class="form-control bg-light" rows="2">{{ old('address') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control bg-light" required>
                        </div>

                        <button type="submit" class="btn btn-zomato w-100 py-3 rounded-3 fw-bold fs-6">Create Account</button>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="text-secondary mb-0">Already registered? <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
