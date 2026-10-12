@extends('layouts.admin')

@section('title', 'Category Management — Sip N Bite Admin')
@section('page_header', 'Category Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Food Categories ({{ count($categories) }})</h5>
    <button class="btn btn-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addCategoryModal">+ Add New Category</button>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Category</th>
                        <th>Description</th>
                        <th>Menu Items</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $cat->image }}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $cat->name }}">
                                    <strong class="text-dark">{{ $cat->name }}</strong>
                                </div>
                            </td>
                            <td class="text-secondary small">{{ $cat->description }}</td>
                            <td><span class="badge bg-secondary rounded-pill px-3 py-1">{{ $cat->menu_items_count }} Items</span></td>
                            <td><span class="badge bg-{{ $cat->status === 'active' ? 'success' : 'danger' }} text-uppercase">{{ $cat->status }}</span></td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.categories.destroy', $cat->id) }}" class="btn btn-sm btn-light text-danger rounded-circle" onclick="return confirm('Delete this category?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-header-title fw-bold mb-0">Add Food Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control bg-light" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image URL</label>
                        <input type="text" name="image" class="form-control bg-light">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control bg-light" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
