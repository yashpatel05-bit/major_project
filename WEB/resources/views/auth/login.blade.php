@extends('layouts.app')

@section('title', 'Login — Sip N Bite')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-danger text-white text-center py-4">
                    <h3 class="fw-bold mb-1"><i class="fa-solid fa-utensils"></i> Sip N Bite</h3>
                    <p class="small mb-0 opacity-75">Login to access your food portal & admin dashboard</p>
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

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-secondary"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-start-0" required value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-secondary"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-start-0" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-zomato w-100 py-3 rounded-3 fw-bold fs-6">Sign In</button>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="text-secondary mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-danger fw-bold text-decoration-none">Create Account</a></p>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 small">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-key text-warning me-1"></i> Demo Credentials:</span>
                        <ul class="mb-0 mt-1 text-secondary ps-3">
                            <li><strong>Admin:</strong> admin@sipnbite.com / admin123</li>
                            <li><strong>Customer:</strong> customer@sipnbite.com / customer123</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
