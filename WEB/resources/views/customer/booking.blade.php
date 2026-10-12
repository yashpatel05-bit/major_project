@extends('layouts.app')

@section('title', 'Book a Table — Sip N Bite Café')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Reservation Form -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-dark text-white p-4">
                    <h3 class="fw-bold brand-font mb-1"><i class="fa-solid fa-chair text-danger me-2"></i> Table Reservation</h3>
                    <p class="small text-secondary mb-0">Reserve your dining table at Sip N Bite for dates & special occasions.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('customer.booking.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Date</label>
                            <input type="date" name="booking_date" class="form-control bg-light" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Time Slot</label>
                                <select name="booking_time" class="form-select bg-light" required>
                                    <option value="12:00 PM">12:00 PM (Lunch)</option>
                                    <option value="01:30 PM">01:30 PM (Lunch)</option>
                                    <option value="04:00 PM">04:00 PM (Evening Snacks)</option>
                                    <option value="07:00 PM">07:00 PM (Dinner)</option>
                                    <option value="08:30 PM" selected>08:30 PM (Dinner)</option>
                                    <option value="10:00 PM">10:00 PM (Late Night)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Number of Guests</label>
                                <input type="number" name="guests_count" class="form-control bg-light" min="1" max="20" value="2" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Table Layout (Optional)</label>
                            <select name="table_id" class="form-select bg-light">
                                <option value="">Any Available Table</option>
                                @foreach($tables as $t)
                                    <option value="{{ $t->id }}">{{ $t->table_number }} (Capacity: {{ $t->capacity }} Guests - {{ $t->location_type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Special Request / Occasion</label>
                            <textarea name="special_request" class="form-control bg-light" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-zomato w-100 py-3 rounded-3 fw-bold fs-6">Confirm Table Reservation <i class="fa-solid fa-calendar-check ms-2"></i></button>
                    </form>
                </div>
            </div>
        </div>

        <!-- My Past & Upcoming Bookings -->
        <div class="col-lg-6">
            <h4 class="fw-bold brand-font mb-3"><i class="fa-solid fa-clock-rotate-left text-danger me-2"></i> My Table Bookings</h4>

            @if($myBookings->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="fa-solid fa-calendar-xmark fs-1 text-secondary opacity-50 mb-3"></i>
                        <h5 class="fw-bold">No Table Reservations</h5>
                        <p class="text-secondary small">Reserve a table using the form on the left to enjoy dining at Sip N Bite.</p>
                    </div>
                </div>
            @else
                <div class="d-flex flex-column gap-3 max-vh-75 overflow-auto">
                    @foreach($myBookings as $b)
                        <div class="card border-0 shadow-sm rounded-4 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-danger rounded-pill px-3 py-1"><i class="fa-solid fa-chair me-1"></i> {{ $b->table ? $b->table->table_number : 'General Table' }}</span>
                                <span class="badge bg-{{ $b->status === 'confirmed' ? 'success' : ($b->status === 'cancelled' ? 'danger' : 'warning') }} text-uppercase px-3 py-1">
                                    {{ $b->status }}
                                </span>
                            </div>
                            <div class="row g-2 small text-secondary">
                                <div class="col-6"><i class="fa-solid fa-calendar me-1 text-danger"></i> {{ $b->booking_date }}</div>
                                <div class="col-6"><i class="fa-solid fa-clock me-1 text-danger"></i> {{ $b->booking_time }}</div>
                                <div class="col-6"><i class="fa-solid fa-user-group me-1 text-danger"></i> {{ $b->guests_count }} Guests</div>
                            </div>
                            @if($b->special_request)
                                <p class="small text-muted mt-2 mb-0 border-top pt-2"><em>Note: {{ $b->special_request }}</em></p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
