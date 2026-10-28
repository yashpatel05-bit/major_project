@extends('layouts.admin')

@section('title', 'Category Management — Sip N Bite Admin')
@section('page_header', 'Category Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
        <h5 class="fw-bold mb-0">Food Categories (<span id="visibleCategoryCount">{{ count($categories) }}</span>)</h5>
        <div id="activeCategoryFilterBadgeContainer" class="d-none">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill d-flex align-items-center gap-2 shadow-sm">
                <i class="fa-solid fa-filter small"></i>
                <span>Showing: <strong id="activeCategoryFilterBadgeText"></strong></span>
                <button type="button" class="btn btn-sm p-0 text-danger border-0 bg-transparent" id="clearActiveCategoryFilterBtn" title="Clear filter and show all categories" style="line-height: 1;">
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
                    id="adminCategoryFilterDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    style="transition: all 0.2s ease;">
                <i class="fa-solid fa-sliders text-danger"></i>
                <span id="currentCategoryFilterTitle">Display On Screen</span>
                <span id="categoryFilterCountBadge" class="badge bg-danger rounded-pill px-2 py-1 ms-1 d-none">1</span>
            </button>
            
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" style="min-width: 320px; max-height: 500px; overflow-y: auto; z-index: 1060;" aria-labelledby="adminCategoryFilterDropdown">
                <!-- Dropdown Header asking Admin -->
                <div class="p-3 bg-light border-bottom rounded-top-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-filter text-danger me-2"></i>Filter Screen Display
                        </h6>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small rounded-pill">Admin</span>
                    </div>
                    <p class="text-secondary small mb-0">Which category do you want to show on the screen only?</p>
                </div>

                <!-- Option: Show All -->
                <div class="p-2 border-bottom">
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-category-screen-filter active" data-filter-type="all" data-filter-value="all" data-filter-label="All Categories">
                        <span class="d-flex align-items-center gap-2 fw-semibold text-dark">
                            <i class="fa-solid fa-border-all text-primary"></i> Show All Categories
                        </span>
                        <span class="badge bg-secondary rounded-pill">{{ count($categories) }}</span>
                    </a>
                </div>

                <!-- Section: Specific Category -->
                <div class="px-3 pt-2 pb-1 text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-layer-group text-danger me-1"></i> Select Specific Category
                </div>
                <div class="p-2 pt-0 border-bottom" style="max-height: 180px; overflow-y: auto;">
                    @foreach($categories as $cat)
                        <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-category-screen-filter" 
                           data-filter-type="category" 
                           data-filter-value="{{ $cat->id }}" 
                           data-filter-label="{{ $cat->name }}">
                            <span class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-folder text-muted small"></i> {{ $cat->name }}
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill">{{ $cat->menu_items_count }} Items</span>
                        </a>
                    @endforeach
                </div>

                <!-- Section: By Status -->
                <div class="px-3 pt-2 pb-1 text-uppercase text-secondary fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-toggle-on text-success me-1"></i> Filter By Status
                </div>
                <div class="p-2 pt-0 border-bottom">
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-category-screen-filter" 
                       data-filter-type="status" 
                       data-filter-value="active" 
                       data-filter-label="Active Categories">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-check text-success small"></i> Active Categories Only
                        </span>
                        <span class="badge bg-success-subtle text-success rounded-pill">{{ $categories->where('status', 'active')->count() }}</span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center justify-content-between admin-category-screen-filter" 
                       data-filter-type="status" 
                       data-filter-value="inactive" 
                       data-filter-label="Inactive Categories">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-xmark text-danger small"></i> Inactive Categories Only
                        </span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $categories->where('status', 'inactive')->count() }}</span>
                    </a>
                </div>

                <!-- Dropdown Footer -->
                <div class="p-2 bg-light rounded-bottom-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted ps-2" id="categoryFilterStatusCaption">Showing all {{ count($categories) }} categories</small>
                    <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold" id="resetCategoryScreenFilterBtn">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Category Button -->
        <button class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa-solid fa-plus me-1"></i> Add New Category
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
                        <th class="ps-4">Category</th>
                        <th>Description</th>
                        <th>Menu Items</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                    @forelse($categories as $cat)
                        <tr class="category-row" data-category-id="{{ $cat->id }}" data-category-status="{{ $cat->status }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $cat->image }}" class="rounded-3 shadow-sm border" style="width: 52px; height: 52px; object-fit: cover;" alt="{{ $cat->name }}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&auto=format&fit=crop&q=60';">
                                    <div>
                                        <strong class="text-dark d-block">{{ $cat->name }}</strong>
                                        <span class="text-muted small">/{{ $cat->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-secondary small">{{ Str::limit($cat->description, 60) ?: 'No description' }}</td>
                            <td><span class="badge bg-secondary rounded-pill px-3 py-1">{{ $cat->menu_items_count }} Items</span></td>
                            <td>
                                <span class="badge bg-{{ $cat->status === 'active' ? 'success' : 'danger' }} text-uppercase">{{ $cat->status }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-sm btn-light text-primary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $cat->id }}" title="Edit Category">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="{{ route('admin.categories.destroy', $cat->id) }}" class="btn btn-sm btn-light text-danger rounded-circle" onclick="return confirm('Delete this category?')" title="Delete Category">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyCategoriesInitialRow">
                            <td colspan="5" class="text-center py-5 text-secondary">
                                <i class="fa-solid fa-layer-group fa-3x mb-3 text-muted"></i>
                                <p class="mb-0">No food categories found. Click "+ Add New Category" to create one.</p>
                            </td>
                        </tr>
                    @endforelse

                    <!-- Empty state row when category filter has 0 matches -->
                    <tr id="noCategoriesFilterMatchRow" class="d-none">
                        <td colspan="5" class="text-center py-5 text-secondary">
                            <i class="fa-solid fa-filter-circle-xmark fa-3x mb-3 text-danger opacity-75"></i>
                            <h6 class="fw-bold text-dark mb-1">No Categories Match This Filter</h6>
                            <p class="small text-muted mb-3">No categories found matching your selection.</p>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" id="emptyStateResetCategoryFilterBtn">
                                <i class="fa-solid fa-rotate-left me-1"></i> Show All Categories
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Category Modals (Rendered outside table for clean valid HTML) -->
@foreach($categories as $cat)
    <div class="modal fade" id="editCategoryModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-header-title fw-bold mb-0"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Food Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST" enctype="multipart/form-data" class="category-submit-form">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-light" value="{{ $cat->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">Current Image</label>
                            <div class="d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                                <img src="{{ $cat->image }}" class="rounded-3 shadow-sm border" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $cat->name }}">
                                <small class="text-muted text-break">{{ Str::limit($cat->image, 45) }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-upload text-danger me-1"></i> Upload New Image (from PC)
                            </label>
                            <input type="file" name="image_file" class="form-control bg-light category-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml">
                            <div class="form-text">Choose a local file from your PC (JPG, PNG, WEBP, up to 50MB).</div>
                            <div class="preview-box mt-2 d-none">
                                <img src="" class="rounded-3 border shadow-sm preview-img" style="width: 80px; height: 80px; object-fit: cover;" alt="Preview">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small">Or change Image URL</label>
                            <input type="text" name="image" class="form-control bg-light" value="{{ Str::startsWith($cat->image, ['http://', 'https://']) ? $cat->image : '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select bg-light">
                                <option value="active" {{ $cat->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $cat->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control bg-light" rows="2">{{ $cat->description }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold submit-btn">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Modal Add Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-header-title fw-bold mb-0">Add Food Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="category-submit-form" novalidate>
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control bg-light" required>
                    </div>

                    <!-- Local Image Upload (Primary Option) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-cloud-arrow-up text-danger me-1"></i> Upload Image from PC
                        </label>
                        <input type="file" name="image_file" id="addCategoryImageFile" class="form-control bg-light category-file-input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml">
                        <div class="form-text">Choose an image file stored locally on your computer (JPG, PNG, WEBP, max 50MB).</div>
                        
                        <!-- Live Image Preview -->
                        <div id="addImagePreviewBox" class="preview-box mt-3 d-none">
                            <div class="d-flex align-items-center gap-3 p-2 bg-white rounded-3 border">
                                <img id="addPreviewImg" src="" class="rounded-3 border shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" alt="Preview">
                                <div class="flex-grow-1">
                                    <span id="addPreviewFileName" class="d-block fw-semibold text-truncate small" style="max-width: 220px;"></span>
                                    <span id="addPreviewFileSize" class="text-muted small"></span>
                                </div>
                                <button type="button" id="clearAddImageBtn" class="btn btn-sm btn-outline-danger rounded-circle" title="Clear chosen image">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Or Image URL (Secondary Option) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small">
                            <i class="fa-solid fa-link me-1"></i> Or Image URL (Optional fallback)
                        </label>
                        <input type="text" name="image" id="addCategoryImageUrl" class="form-control bg-light">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control bg-light" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold submit-btn">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preview for Add Category modal
    const addFileInput = document.getElementById('addCategoryImageFile');
    const addPreviewBox = document.getElementById('addImagePreviewBox');
    const addPreviewImg = document.getElementById('addPreviewImg');
    const addPreviewFileName = document.getElementById('addPreviewFileName');
    const addPreviewFileSize = document.getElementById('addPreviewFileSize');
    const clearAddImageBtn = document.getElementById('clearAddImageBtn');

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

    // Previews for Edit Category modals
    document.querySelectorAll('.modal .category-file-input').forEach(function (input) {
        if (input.id === 'addCategoryImageFile') return;
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

    // Provide instant visual feedback when submitting (uploading large files can take a second)
    document.querySelectorAll('.category-submit-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitBtn = this.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Saving...';
            }
        });
    });

    // ===============================================
    // Admin Screen Display Category Filter Logic
    // ===============================================
    const categoryFilterLinks = document.querySelectorAll('.admin-category-screen-filter');
    const categoryRows = document.querySelectorAll('.category-row');
    const visibleCategoryCountEl = document.getElementById('visibleCategoryCount');
    const currentCategoryFilterTitle = document.getElementById('currentCategoryFilterTitle');
    const categoryFilterCountBadge = document.getElementById('categoryFilterCountBadge');
    const activeCategoryBadgeContainer = document.getElementById('activeCategoryFilterBadgeContainer');
    const activeCategoryBadgeText = document.getElementById('activeCategoryFilterBadgeText');
    const clearActiveCategoryBtn = document.getElementById('clearActiveCategoryFilterBtn');
    const resetCategoryBtn = document.getElementById('resetCategoryScreenFilterBtn');
    const emptyStateResetCategoryBtn = document.getElementById('emptyStateResetCategoryFilterBtn');
    const noCategoryMatchRow = document.getElementById('noCategoriesFilterMatchRow');
    const categoryFilterStatusCaption = document.getElementById('categoryFilterStatusCaption');
    const totalCategories = categoryRows.length;

    function applyCategoryScreenFilter(type, value, label) {
        let visibleCount = 0;

        categoryRows.forEach(function (row) {
            let matches = false;
            if (type === 'all') {
                matches = true;
            } else if (type === 'category') {
                matches = (row.getAttribute('data-category-id') === String(value));
            } else if (type === 'status') {
                matches = (row.getAttribute('data-category-status') === String(value));
            }

            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counter in page header
        if (visibleCategoryCountEl) {
            visibleCategoryCountEl.textContent = visibleCount;
        }

        // Toggle empty state if no rows match
        if (noCategoryMatchRow) {
            if (visibleCount === 0 && totalCategories > 0) {
                noCategoryMatchRow.classList.remove('d-none');
            } else {
                noCategoryMatchRow.classList.add('d-none');
            }
        }

        // Update active class on dropdown links
        categoryFilterLinks.forEach(function (link) {
            const linkType = link.getAttribute('data-filter-type');
            const linkVal = link.getAttribute('data-filter-value');
            if (linkType === type && String(linkVal) === String(value)) {
                link.classList.add('active', 'bg-danger', 'text-white');
                const badge = link.querySelector('.badge');
                if (badge) {
                    badge.className = 'badge bg-white text-danger rounded-pill';
                }
            } else {
                link.classList.remove('active', 'bg-danger', 'text-white');
                const badge = link.querySelector('.badge');
                if (badge) {
                    if (linkType === 'all') badge.className = 'badge bg-secondary rounded-pill';
                    else if (linkType === 'category') badge.className = 'badge bg-light text-dark border rounded-pill';
                    else if (linkType === 'status' && linkVal === 'active') badge.className = 'badge bg-success-subtle text-success rounded-pill';
                    else if (linkType === 'status' && linkVal === 'inactive') badge.className = 'badge bg-danger-subtle text-danger rounded-pill';
                }
            }
        });

        // Update top-right button label and active pill badge
        if (type === 'all') {
            if (currentCategoryFilterTitle) currentCategoryFilterTitle.textContent = 'Display On Screen';
            if (categoryFilterCountBadge) categoryFilterCountBadge.classList.add('d-none');
            if (activeCategoryBadgeContainer) activeCategoryBadgeContainer.classList.add('d-none');
            if (categoryFilterStatusCaption) categoryFilterStatusCaption.textContent = 'Showing all ' + totalCategories + ' categories';
        } else {
            if (currentCategoryFilterTitle) currentCategoryFilterTitle.textContent = 'Showing: ' + label;
            if (categoryFilterCountBadge) {
                categoryFilterCountBadge.classList.remove('d-none');
                categoryFilterCountBadge.textContent = visibleCount;
            }
            if (activeCategoryBadgeContainer) {
                activeCategoryBadgeContainer.classList.remove('d-none');
                if (activeCategoryBadgeText) activeCategoryBadgeText.textContent = label + ' (' + visibleCount + ' categories)';
            }
            if (categoryFilterStatusCaption) categoryFilterStatusCaption.textContent = 'Showing ' + visibleCount + ' of ' + totalCategories + ' categories';
        }
    }

    categoryFilterLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const type = this.getAttribute('data-filter-type');
            const value = this.getAttribute('data-filter-value');
            const label = this.getAttribute('data-filter-label');
            applyCategoryScreenFilter(type, value, label);
        });
    });

    if (clearActiveCategoryBtn) {
        clearActiveCategoryBtn.addEventListener('click', function () {
            applyCategoryScreenFilter('all', 'all', 'All Categories');
        });
    }
    if (resetCategoryBtn) {
        resetCategoryBtn.addEventListener('click', function () {
            applyCategoryScreenFilter('all', 'all', 'All Categories');
        });
    }
    if (emptyStateResetCategoryBtn) {
        emptyStateResetCategoryBtn.addEventListener('click', function () {
            applyCategoryScreenFilter('all', 'all', 'All Categories');
        });
    }
});
</script>
@endsection
