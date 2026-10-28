@extends('layouts.admin')

@section('title', 'Menu Items Management — Sip N Bite Admin')
@section('page_header', 'Menu Items Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <h5 class="fw-bold mb-0">All Menu Dishes (<span id="visibleDishCount">{{ count($menuItems) }}</span>)</h5>
        <div id="activeFilterBadgeContainer" class="d-none">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm">
                <i class="fa-solid fa-filter small"></i>
                <span>Showing: <strong id="activeFilterBadgeText"></strong></span>
                <button type="button" class="btn btn-sm p-0 text-danger border-0 bg-transparent" id="clearActiveFilterBtn" title="Clear filter and show all dishes" style="line-height: 1;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 ms-auto">
        <!-- Display Filter Dropdown Button for Admin -->
        <div class="dropdown">
            <button class="btn btn-white bg-white border border-secondary-subtle rounded-pill px-3 py-2 fw-semibold shadow-sm dropdown-toggle d-flex align-items-center gap-2" 
                    type="button" 
                    id="adminDisplayFilterDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    style="transition: all 0.2s ease;">
                <i class="fa-solid fa-sliders text-danger"></i>
                <span id="currentDisplayFilterTitle">Display On Screen</span>
                <span id="filterCountBadge" class="badge bg-danger rounded-pill px-2 py-1 ms-1 d-none">1</span>
            </button>
            
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" style="min-width: 340px; max-height: 520px; overflow-y: auto; z-index: 1060;" aria-labelledby="adminDisplayFilterDropdown">
                <!-- Dropdown Header asking Admin -->
                <div class="p-3 bg-light border-bottom rounded-top-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-filter text-danger me-2"></i>Filter Screen Display
                        </h6>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small rounded-pill">Admin</span>
                    </div>
                    <p class="text-secondary small mb-0">Which category or food type do you want to show on the screen only?</p>
                </div>

                <!-- Option: Show All -->
                <div class="p-2 border-bottom">
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-screen-filter active" data-filter-type="all" data-filter-value="all" data-filter-label="All Menu Dishes">
                        <span class="d-flex align-items-center gap-2 fw-semibold text-dark">
                            <i class="fa-solid fa-border-all text-primary"></i> Show All Food Dishes
                        </span>
                        <span class="badge bg-secondary rounded-pill">{{ count($menuItems) }}</span>
                    </a>
                </div>

                <!-- Section: Category -->
                <div class="px-3 pt-2 pb-1 text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-layer-group text-danger me-1"></i> Filter By Category
                </div>
                <div class="p-2 pt-0 border-bottom" style="max-height: 180px; overflow-y: auto;">
                    @foreach($categories as $cat)
                        @php
                            $catCount = $menuItems->where('category_id', $cat->id)->count();
                        @endphp
                        <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-screen-filter" 
                           data-filter-type="category" 
                           data-filter-value="{{ $cat->id }}" 
                           data-filter-label="{{ $cat->name }}">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-folder text-muted small"></i> {{ $cat->name }}
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill">{{ $catCount }}</span>
                        </a>
                    @endforeach
                </div>

                <!-- Section: Food Type (Veg / Non-Veg) -->
                <div class="px-3 pt-2 pb-1 text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-leaf text-success me-1"></i> Filter By Food Type
                </div>
                <div class="p-2 pt-0 border-bottom">
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-screen-filter" 
                       data-filter-type="food_type" 
                       data-filter-value="veg" 
                       data-filter-label="Pure Veg">
                        <span class="d-flex align-items-center gap-2">
                            <span class="border border-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 16px; height: 16px;">
                                <span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                            </span>
                            Pure Veg Food Only
                        </span>
                        <span class="badge bg-success-subtle text-success rounded-pill">{{ $menuItems->where('is_veg', true)->count() }}</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-screen-filter" 
                       data-filter-type="food_type" 
                       data-filter-value="non_veg" 
                       data-filter-label="Non-Veg">
                        <span class="d-flex align-items-center gap-2">
                            <span class="border border-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 16px; height: 16px;">
                                <span class="bg-danger rounded-circle" style="width: 8px; height: 8px;"></span>
                            </span>
                            Non-Veg Food Only
                        </span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $menuItems->where('is_veg', false)->count() }}</span>
                    </a>
                </div>

                <!-- Section: Availability -->
                <div class="px-3 pt-2 pb-1 text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-boxes-stacked text-warning me-1"></i> Filter By Availability
                </div>
                <div class="p-2 pt-0 border-bottom">
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-screen-filter" 
                       data-filter-type="stock" 
                       data-filter-value="in_stock" 
                       data-filter-label="In Stock">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-check text-success small"></i> In Stock Dishes Only
                        </span>
                        <span class="badge bg-success-subtle text-success rounded-pill">{{ $menuItems->where('is_available', true)->count() }}</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-screen-filter" 
                       data-filter-type="stock" 
                       data-filter-value="out_of_stock" 
                       data-filter-label="Out of Stock">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-ban text-secondary small"></i> Out of Stock Dishes Only
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill">{{ $menuItems->where('is_available', false)->count() }}</span>
                    </a>
                </div>

                <!-- Dropdown Footer -->
                <div class="p-2 bg-light rounded-bottom-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted ps-2" id="filterStatusCaption">Showing all {{ count($menuItems) }} items</small>
                    <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold" id="resetScreenFilterBtn">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Dish Button -->
        <button class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMenuModal">
            <i class="fa-solid fa-plus me-1"></i> Add Dish
        </button>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <strong class="d-block mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please check the form errors:</strong>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
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
                <tbody id="menuDishesTableBody">
                    @forelse($menuItems as $item)
                        <tr class="menu-dish-row" 
                            data-category-id="{{ $item->category_id }}" 
                            data-category-name="{{ strtolower($item->category->name ?? '') }}" 
                            data-is-veg="{{ $item->is_veg ? '1' : '0' }}" 
                            data-is-available="{{ $item->is_available ? '1' : '0' }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $item->image }}" class="rounded-3 shadow-sm border" style="width: 52px; height: 52px; object-fit: cover;" alt="{{ $item->name }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=60';">
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
                                <button type="button" class="btn btn-sm btn-light text-primary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editMenuModal{{ $item->id }}" title="Edit Dish">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="{{ route('admin.menu.destroy', $item->id) }}" class="btn btn-sm btn-light text-danger rounded-circle" onclick="return confirm('Delete this menu dish?')" title="Delete Dish">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyTableInitialRow">
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-utensils fa-3x mb-3 text-muted"></i>
                                <p class="mb-0">No dishes found. Click "+ Add Dish" to add your first menu item.</p>
                            </td>
                        </tr>
                    @endforelse

                    <!-- Empty state row when filter has 0 matches -->
                    <tr id="noDishesFilterMatchRow" class="d-none">
                        <td colspan="6" class="text-center py-5 text-secondary">
                            <i class="fa-solid fa-filter-circle-xmark fa-3x mb-3 text-danger opacity-75"></i>
                            <h6 class="fw-bold text-dark mb-1">No Dishes Match This Filter</h6>
                            <p class="small text-muted mb-3">No menu dishes found for the selected category or food type on your screen.</p>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" id="emptyStateResetFilterBtn">
                                <i class="fa-solid fa-rotate-left me-1"></i> Show All Food Dishes
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Menu Item Modals (Outside table for valid HTML5) -->
@foreach($menuItems as $item)
    <div class="modal fade" id="editMenuModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-header-title fw-bold mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Menu Dish</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.menu.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="menu-submit-form">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Dish Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control bg-light" value="{{ $item->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select bg-light" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-control bg-light" value="{{ $item->price }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Rating (1 to 5)</label>
                                <input type="number" step="0.1" min="1" max="5" name="rating" class="form-control bg-light" value="{{ $item->rating }}">
                            </div>
                        </div>

                        <!-- Current Image Display -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Current Image</label>
                            <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                                <img src="{{ $item->image }}" class="rounded-3 shadow-sm border" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->name }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=60';">
                                <small class="text-muted text-break">{{ Str::limit($item->image, 50) }}</small>
                            </div>
                        </div>

                        <!-- Local Image Upload (From PC) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-cloud-arrow-up text-danger me-1"></i> Upload New Image (from PC)
                            </label>
                            <input type="file" name="image_file" class="form-control bg-light menu-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml">
                            <div class="form-text">Choose a local image from your computer (JPG, PNG, WEBP, up to 50MB).</div>
                            <div class="preview-box mt-2 d-none">
                                <img src="" class="rounded-3 border shadow-sm preview-img" style="width: 80px; height: 80px; object-fit: cover;" alt="Preview">
                            </div>
                        </div>

                        <!-- Or Image URL (Secondary) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">
                                <i class="fa-solid fa-link me-1"></i> Or Image URL (Optional)
                            </label>
                            <input type="text" name="image" class="form-control bg-light" value="{{ Str::startsWith($item->image, ['http://', 'https://']) ? $item->image : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control bg-light" rows="2">{{ $item->description }}</textarea>
                        </div>

                        <div class="d-flex gap-4 p-3 bg-light rounded-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_veg" id="is_veg_{{ $item->id }}" {{ $item->is_veg ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_veg_{{ $item->id }}">Vegetarian Dish</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_available" id="is_available_{{ $item->id }}" {{ $item->is_available ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_available_{{ $item->id }}">Available for Order</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold submit-btn">Update Menu Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Add Menu Item -->
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-header-title fw-bold mb-0">Add New Menu Dish</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="menu-submit-form" novalidate>
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dish Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select bg-light" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control bg-light" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rating (1 to 5)</label>
                            <input type="number" step="0.1" min="1" max="5" name="rating" class="form-control bg-light" value="4.8">
                        </div>
                    </div>

                    <!-- Local Image Upload from PC (Primary) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-cloud-arrow-up text-danger me-1"></i> Upload Dish Image (from PC)
                        </label>
                        <input type="file" name="image_file" id="addMenuImageFile" class="form-control bg-light menu-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml">
                        <div class="form-text">Choose an image from your computer (JPG, PNG, WEBP, up to 50MB).</div>
                        
                        <!-- Live Image Preview -->
                        <div id="addMenuPreviewBox" class="preview-box mt-3 d-none">
                            <div class="d-flex align-items-center gap-3 p-2 bg-white rounded-3 border">
                                <img id="addMenuPreviewImg" src="" class="rounded-3 border shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" alt="Preview">
                                <div class="flex-grow-1">
                                    <span id="addMenuPreviewFileName" class="d-block fw-semibold text-truncate small" style="max-width: 260px;"></span>
                                    <span id="addMenuPreviewFileSize" class="text-muted small"></span>
                                </div>
                                <button type="button" id="clearAddMenuImageBtn" class="btn btn-sm btn-outline-danger rounded-circle" title="Clear chosen image">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Or Image URL (Secondary) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">
                            <i class="fa-solid fa-link me-1"></i> Or Image URL (Optional)
                        </label>
                        <input type="text" name="image" id="addMenuImageUrl" class="form-control bg-light">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control bg-light" rows="2"></textarea>
                    </div>

                    <div class="d-flex gap-4 p-3 bg-light rounded-3">
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
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold submit-btn">Save Menu Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add Menu Dish preview
    const addFileInput = document.getElementById('addMenuImageFile');
    const addPreviewBox = document.getElementById('addMenuPreviewBox');
    const addPreviewImg = document.getElementById('addMenuPreviewImg');
    const addPreviewFileName = document.getElementById('addMenuPreviewFileName');
    const addPreviewFileSize = document.getElementById('addMenuPreviewFileSize');
    const clearAddImageBtn = document.getElementById('clearAddMenuImageBtn');

    if (addFileInput) {
        addFileInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (file) {
                if (file.size > 50 * 1024 * 1024) {
                    alert('The selected image is ' + (file.size / (1024 * 1024)).toFixed(1) + 'MB. Maximum allowed file size is 50MB.');
                    this.value = '';
                    addPreviewBox.classList.add('d-none');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    addPreviewImg.src = e.target.result;
                    addPreviewFileName.textContent = file.name;
                    const sizeStr = file.size >= 1048576 
                        ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' 
                        : (file.size / 1024).toFixed(1) + ' KB';
                    addPreviewFileSize.textContent = sizeStr;
                    addPreviewBox.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                addPreviewBox.classList.add('d-none');
            }
        });
    }

    if (clearAddImageBtn) {
        clearAddImageBtn.addEventListener('click', function () {
            addFileInput.value = '';
            addPreviewBox.classList.add('d-none');
            addPreviewImg.src = '';
        });
    }

    // Previews for Edit Menu Item modals
    document.querySelectorAll('.modal .menu-file-input').forEach(function (input) {
        if (input.id === 'addMenuImageFile') return;
        input.addEventListener('change', function () {
            const previewBox = this.closest('.modal-body').querySelector('.preview-box');
            const previewImg = previewBox ? previewBox.querySelector('.preview-img') : null;
            const file = this.files && this.files[0];
            if (file) {
                if (file.size > 50 * 1024 * 1024) {
                    alert('The selected image is ' + (file.size / (1024 * 1024)).toFixed(1) + 'MB. Maximum allowed file size is 50MB.');
                    this.value = '';
                    if (previewBox) previewBox.classList.add('d-none');
                    return;
                }
                if (previewBox && previewImg) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        previewBox.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            } else if (previewBox) {
                previewBox.classList.add('d-none');
            }
        });
    });

    // Provide instant visual feedback when submitting
    document.querySelectorAll('.menu-submit-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitBtn = this.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Saving...';
            }
        });
    });

    // ==========================================
    // Admin Screen Display Filter Dropdown Logic
    // ==========================================
    const filterLinks = document.querySelectorAll('.admin-screen-filter');
    const dishRows = document.querySelectorAll('.menu-dish-row');
    const visibleCountEl = document.getElementById('visibleDishCount');
    const filterTitleEl = document.getElementById('currentDisplayFilterTitle');
    const filterBadgeEl = document.getElementById('filterCountBadge');
    const activeBadgeContainer = document.getElementById('activeFilterBadgeContainer');
    const activeBadgeText = document.getElementById('activeFilterBadgeText');
    const clearActiveBtn = document.getElementById('clearActiveFilterBtn');
    const resetBtn = document.getElementById('resetScreenFilterBtn');
    const emptyStateResetBtn = document.getElementById('emptyStateResetFilterBtn');
    const noMatchRow = document.getElementById('noDishesFilterMatchRow');
    const filterStatusCaption = document.getElementById('filterStatusCaption');
    const totalDishes = dishRows.length;

    function applyScreenFilter(type, value, label) {
        let visibleCount = 0;

        dishRows.forEach(function (row) {
            let matches = false;
            if (type === 'all') {
                matches = true;
            } else if (type === 'category') {
                matches = (row.getAttribute('data-category-id') === String(value));
            } else if (type === 'food_type') {
                if (value === 'veg') {
                    matches = (row.getAttribute('data-is-veg') === '1');
                } else if (value === 'non_veg') {
                    matches = (row.getAttribute('data-is-veg') === '0');
                }
            } else if (type === 'stock') {
                if (value === 'in_stock') {
                    matches = (row.getAttribute('data-is-available') === '1');
                } else if (value === 'out_of_stock') {
                    matches = (row.getAttribute('data-is-available') === '0');
                }
            }

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counter in page header
        if (visibleCountEl) {
            visibleCountEl.textContent = visibleCount;
        }

        // Toggle empty state if no rows match
        if (noMatchRow) {
            if (visibleCount === 0 && totalDishes > 0) {
                noMatchRow.classList.remove('d-none');
            } else {
                noMatchRow.classList.add('d-none');
            }
        }

        // Update active class on dropdown links
        filterLinks.forEach(function (link) {
            const linkType = link.getAttribute('data-filter-type');
            const linkVal = link.getAttribute('data-filter-value');
            if (linkType === type && String(linkVal) === String(value)) {
                link.classList.add('active', 'bg-danger', 'text-white');
                const badge = link.querySelector('.badge');
                if (badge) {
                    badge.dataset.prevClass = badge.className;
                    badge.className = 'badge bg-white text-danger rounded-pill';
                }
            } else {
                link.classList.remove('active', 'bg-danger', 'text-white');
                const badge = link.querySelector('.badge');
                if (badge) {
                    if (linkType === 'all') badge.className = 'badge bg-secondary rounded-pill';
                    else if (linkType === 'category') badge.className = 'badge bg-light text-dark border rounded-pill';
                    else if (linkType === 'food_type' && linkVal === 'veg') badge.className = 'badge bg-success-subtle text-success rounded-pill';
                    else if (linkType === 'food_type' && linkVal === 'non_veg') badge.className = 'badge bg-danger-subtle text-danger rounded-pill';
                    else if (linkType === 'stock') badge.className = 'badge bg-secondary rounded-pill';
                }
            }
        });

        // Update top-right button label and active pill badge
        if (type === 'all') {
            if (filterTitleEl) filterTitleEl.textContent = 'Display On Screen';
            if (filterBadgeEl) filterBadgeEl.classList.add('d-none');
            if (activeBadgeContainer) activeBadgeContainer.classList.add('d-none');
            if (filterStatusCaption) filterStatusCaption.textContent = 'Showing all ' + totalDishes + ' items';
        } else {
            if (filterTitleEl) filterTitleEl.textContent = 'Showing: ' + label;
            if (filterBadgeEl) {
                filterBadgeEl.classList.remove('d-none');
                filterBadgeEl.textContent = visibleCount;
            }
            if (activeBadgeContainer) {
                activeBadgeContainer.classList.remove('d-none');
                if (activeBadgeText) activeBadgeText.textContent = label + ' (' + visibleCount + ' dishes)';
            }
            if (filterStatusCaption) filterStatusCaption.textContent = 'Showing ' + visibleCount + ' of ' + totalDishes + ' items';
        }
    }

    filterLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const type = this.getAttribute('data-filter-type');
            const value = this.getAttribute('data-filter-value');
            const label = this.getAttribute('data-filter-label');
            applyScreenFilter(type, value, label);
        });
    });

    if (clearActiveBtn) {
        clearActiveBtn.addEventListener('click', function () {
            applyScreenFilter('all', 'all', 'All Menu Dishes');
        });
    }
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            applyScreenFilter('all', 'all', 'All Menu Dishes');
        });
    }
    if (emptyStateResetBtn) {
        emptyStateResetBtn.addEventListener('click', function () {
            applyScreenFilter('all', 'all', 'All Menu Dishes');
        });
    }
});
</script>
@endsection
