@extends('layouts.app')

@section('title', 'Menu Catalog — Sip N Bite')

@section('content')
<div class="container py-4">
    <!-- Header & Search Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <h3 class="fw-bold brand-font mb-0"><i class="fa-solid fa-book-open text-danger me-2"></i> Our Menu</h3>
                <p class="text-secondary small mb-0">Discover handcrafted burgers, pizzas, coffees & treats</p>
            </div>
            <div class="col-md-8">
                <form action="{{ route('customer.menu') }}" method="GET" class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control rounded-pill bg-light" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select rounded-pill bg-light">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-zomato w-100 rounded-pill">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter Tags -->
        <div class="d-flex gap-2 mt-3 pt-3 border-top">
            <a href="{{ route('customer.menu') }}" class="btn btn-sm rounded-pill {{ !request('type') ? 'btn-danger' : 'btn-outline-secondary' }}">All Dishes</a>
            <a href="{{ route('customer.menu', array_merge(request()->query(), ['type' => 'veg'])) }}" class="btn btn-sm rounded-pill {{ request('type') == 'veg' ? 'btn-success' : 'btn-outline-success' }}"><span class="veg-badge me-1"></span> Veg Only</a>
            <a href="{{ route('customer.menu', array_merge(request()->query(), ['type' => 'nonveg'])) }}" class="btn btn-sm rounded-pill {{ request('type') == 'nonveg' ? 'btn-danger' : 'btn-outline-danger' }}"><span class="nonveg-badge me-1"></span> Non-Veg Only</a>
        </div>
    </div>

    <!-- Food Catalog Grid -->
    @if($menuItems->isEmpty())
        <div class="text-center py-5">
            <i class="fa-solid fa-utensils fs-1 text-secondary mb-3"></i>
            <h5>No dishes found matching your criteria</h5>
            <p class="text-secondary">Try searching for something else or clearing filters.</p>
            <a href="{{ route('customer.menu') }}" class="btn btn-outline-danger rounded-pill">Clear Filters</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($menuItems as $item)
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
                            <p class="text-secondary small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $item->description }}</p>
                            
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
    @endif
</div>
@endsection
