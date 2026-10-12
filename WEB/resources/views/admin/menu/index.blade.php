@extends('layouts.admin')

@section('title', 'Menu Items Management — Sip N Bite Admin')
@section('page_header', 'Menu Items Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">All Menu Dishes ({{ count($menuItems) }})</h5>
    <button class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addMenuModal">+ Add Dish</button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Dish</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Availability</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($menuItems as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->image }}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->name }}">
                                    <div>
                                        <strong class="text-dark d-block">{{ $item->name }}</strong>
                                        <span class="small text-secondary"><i class="fa-solid fa-star text-warning me-1"></i> {{ number_format($item->rating, 1) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border px-3 py-1">{{ $item->category ? $item->category->name : 'Uncategorized' }}</span></td>
                            <td class="fw-bold text-danger">₹{{ number_format($item->price, 2) }}</td>
                            <td>
                                @if($item->is_veg)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Veg</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">Non-Veg</span>
                                @endif
                            </td>
                            <td>
                                @if($item->is_available)
                                    <span class="badge bg-success text-uppercase">In Stock</span>
                                @else
                                    <span class="badge bg-secondary text-uppercase">Out of Stock</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.menu.destroy', $item->id) }}" class="btn btn-sm btn-light text-danger rounded-circle" onclick="return confirm('Delete this menu dish?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Menu Item -->
<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-header-title fw-bold mb-0">Add New Menu Dish</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.menu.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dish Name</label>
                            <input type="text" name="name" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category_id" class="form-select bg-light" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rating (1 to 5)</label>
                            <input type="number" step="0.1" name="rating" class="form-control bg-light" value="4.8">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image URL</label>
                        <input type="text" name="image" class="form-control bg-light">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control bg-light" rows="2"></textarea>
                    </div>

                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_veg" id="is_veg" checked>
                            <label class="form-check-label fw-semibold" for="is_veg">Vegetarian Dish</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_available" id="is_available" checked>
                            <label class="form-check-label fw-semibold" for="is_available">Available for Order</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Save Menu Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
