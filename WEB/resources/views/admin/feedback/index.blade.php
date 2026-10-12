@extends('layouts.admin')

@section('title', 'Customer Feedback & Ratings — Sip N Bite Admin')
@section('page_header', 'Customer Ratings & Reviews')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card p-4">
            <span class="text-secondary fw-semibold">Average Customer Rating</span>
            <h2 class="fw-bold mb-0 text-dark mt-2"><i class="fa-solid fa-star text-warning me-2"></i> {{ number_format($averageRating, 1) }} / 5.0</h2>
            <span class="small text-secondary">Based on customer order reviews</span>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Customer</th>
                        <th>Order #</th>
                        <th>Rating</th>
                        <th>Review Comment</th>
                        <th class="pe-4 text-end">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $fb)
                        <tr>
                            <td class="ps-4">
                                <strong class="text-dark d-block">{{ $fb->user ? $fb->user->name : 'Customer' }}</strong>
                                <span class="small text-secondary">{{ $fb->user ? $fb->user->email : '' }}</span>
                            </td>
                            <td class="fw-bold">#{{ $fb->order ? $fb->order->order_number : 'General' }}</td>
                            <td>
                                <span class="badge bg-warning text-dark fw-bold px-3 py-1 fs-6">
                                    ⭐ {{ $fb->rating }} / 5
                                </span>
                            </td>
                            <td class="text-dark" style="max-width: 300px;">"{{ $fb->comment }}"</td>
                            <td class="pe-4 text-end small text-secondary">{{ $fb->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-secondary">No customer feedback reviews submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
