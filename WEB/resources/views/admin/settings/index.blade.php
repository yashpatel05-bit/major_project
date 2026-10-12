@extends('layouts.admin')

@section('title', 'Café Settings — Sip N Bite Admin')
@section('page_header', 'Restaurant Information & Settings')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-store text-danger me-2"></i> Café Details & Configuration</h5>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Café Name</label>
                        <input type="text" name="cafe_name" class="form-control bg-light" value="{{ $settings['cafe_name'] }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tagline</label>
                        <input type="text" name="cafe_tagline" class="form-control bg-light" value="{{ $settings['cafe_tagline'] }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Phone</label>
                        <input type="text" name="cafe_phone" class="form-control bg-light" value="{{ $settings['cafe_phone'] }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contact Email</label>
                        <input type="email" name="cafe_email" class="form-control bg-light" value="{{ $settings['cafe_email'] }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Physical Address</label>
                    <textarea name="cafe_address" class="form-control bg-light" rows="2">{{ $settings['cafe_address'] }}</textarea>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Opening Hours</label>
                        <input type="text" name="opening_hours" class="form-control bg-light" value="{{ $settings['opening_hours'] }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tax GST (%)</label>
                        <input type="number" name="tax_percentage" class="form-control bg-light" value="{{ $settings['tax_percentage'] }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Flat Delivery Fee (₹)</label>
                        <input type="number" name="flat_delivery_fee" class="form-control bg-light" value="{{ $settings['flat_delivery_fee'] }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Save Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection
