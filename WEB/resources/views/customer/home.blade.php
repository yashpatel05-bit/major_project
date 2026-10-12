@extends('layouts.app')

@section('title', 'Sip N Bite — Zomato Food Ordering & Table Reservation')

@section('content')
<!-- Hero Section -->
<div class="position-relative bg-dark text-white py-5 mb-5" style="background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1600&auto=format&fit=crop&q=80') center/cover no-repeat;">
    <div class="container py-5 text-center">
        <h1 class="display-3 brand-font fw-bold text-danger mb-3"><i class="fa-solid fa-utensils"></i> Sip N Bite</h1>
        <p class="fs-4 text-light fw-light mb-4">Discover the finest burgers, wood-fired pizzas, artisanal coffees & table dining.</p>
        
        <!-- Search Box -->
        <div class="row justify-content-center">
            <div class="col-md-7">
                <form action="{{ route('customer.menu') }}" method="GET" class="d-flex bg-white p-2 rounded-pill shadow-lg">
                    <span class="input-group-text bg-transparent border-0 text-secondary px-3"><i class="fa-solid fa-magnifying-glass text-danger fs-5"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none fs-6">
                    <button type="submit" class="btn btn-zomato rounded-pill px-4">Search</button>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="{{ route('customer.menu') }}" class="btn btn-zomato rounded-pill px-4 py-2"><i class="fa-solid fa-truck-fast me-2"></i> Delivery Menu</a>
            <a href="{{ route('customer.booking') }}" class="btn btn-outline-light rounded-pill px-4 py-2"><i class="fa-solid fa-calendar-check me-2"></i> Book a Table</a>
        </div>
    </div>
</div>

<div class="container">
    <!-- Food Categories Grid -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold brand-font mb-1">Inspiration for your first order</h3>
                <p class="text-secondary mb-0">Explore curated food categories & chef specialties</p>
            </div>
            <a href="{{ route('customer.menu') }}" class="text-danger fw-bold text-decoration-none">See All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            @foreach($categories as $cat)
                <div class="col-6 col-md-3">
                    <a href="{{ route('customer.menu', ['category' => $cat->id]) }}" class="text-decoration-none text-dark">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 food-card">
                            <img src="{{ $cat->image }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $cat->name }}">
                            <div class="card-body text-center py-3">
                                <h6 class="fw-bold mb-1">{{ $cat->name }}</h6>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small">Explore</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Popular Menu Items -->
    <div class="mb-5">
        <h3 class="fw-bold brand-font mb-1">Top Rated Dishes</h3>
        <p class="text-secondary mb-4">Most ordered & loved by Sip N Bite foodies</p>

        <div class="row g-4">
            @foreach($popularItems as $item)
                <div class="col-md-3">
                    <div class="card food-card h-100 border-0">
                        <div class="position-relative">
                            <img src="{{ $item->image }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $item->name }}">
                            <span class="position-absolute top-0 end-0 bg-dark text-white rounded-pill px-2 py-1 m-2 small fw-bold"><i class="fa-solid fa-star text-warning me-1"></i> {{ number_format($item->rating, 1) }}</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="{{ $item->is_veg ? 'veg-badge' : 'nonveg-badge' }}"></span>
                                <h6 class="fw-bold mb-0 text-truncate">{{ $item->name }}</h6>
                            </div>
                            <p class="text-secondary small flex-grow-1 text-truncate-2">{{ $item->description }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <span class="fw-bold fs-5 text-dark">₹{{ number_format($item->price, 0) }}</span>
                                <form action="{{ route('customer.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">+ ADD</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Dining Table Banner -->
    <div class="card border-0 rounded-4 bg-dark text-white p-4 p-md-5 my-5 shadow-lg" style="background: linear-gradient(90deg, #111827 0%, #1f2937 100%);">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-2 rounded-pill mb-2"><i class="fa-solid fa-utensils me-1"></i> Premium Dining</span>
                <h2 class="brand-font fw-bold text-white mb-2">Reserve a Gourmet Table</h2>
                <p class="text-secondary fs-5 mb-0">Skip the waiting line! Book indoor, outdoor patio, or rooftop tables for date nights & celebrations.</p>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <a href="{{ route('customer.booking') }}" class="btn btn-zomato btn-lg rounded-pill px-4 fw-bold">Book Now <i class="fa-solid fa-calendar-days ms-2"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
