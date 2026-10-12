<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard — Sip N Bite')</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome & Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #111827;
            --sidebar-hover: #1f2937;
            --primary-accent: #E23744;
            --bg-canvas: #f3f4f6;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            color: #9ca3af;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .admin-sidebar .nav-link {
            color: #9ca3af;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.2s ease;
        }

        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--primary-accent);
        }

        .main-wrapper {
            margin-left: 260px;
            padding: 30px;
        }

        .stat-card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar d-flex flex-column py-3">
        <div class="px-4 mb-4 mt-2">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <span class="fs-4 fw-bold text-white"><i class="fa-solid fa-utensils text-danger me-2"></i>Sip N Bite</span>
                <span class="badge bg-danger text-uppercase px-2 py-1 fs-6">Admin</span>
            </a>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie me-3"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><i class="fa-solid fa-basket-shopping me-3"></i> Orders</a></li>
            <li><a href="{{ route('admin.menu.index') }}" class="nav-link {{ request()->routeIs('admin.menu*') ? 'active' : '' }}"><i class="fa-solid fa-burger me-3"></i> Menu Items</a></li>
            <li><a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group me-3"></i> Categories</a></li>
            <li><a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"><i class="fa-solid fa-chair me-3"></i> Table Bookings</a></li>
            <li><a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}"><i class="fa-solid fa-users me-3"></i> Customers</a></li>
            <li><a href="{{ route('admin.delivery.index') }}" class="nav-link {{ request()->routeIs('admin.delivery*') ? 'active' : '' }}"><i class="fa-solid fa-truck-ramping me-3"></i> Delivery Staff</a></li>
            <li><a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"><i class="fa-solid fa-credit-card me-3"></i> Payments</a></li>
            <li><a href="{{ route('admin.feedback.index') }}" class="nav-link {{ request()->routeIs('admin.feedback*') ? 'active' : '' }}"><i class="fa-solid fa-star me-3"></i> Ratings & Reviews</a></li>
            <li><a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i class="fa-solid fa-gear me-3"></i> Settings</a></li>
        </ul>

        <div class="px-3 mt-auto">
            <a href="{{ route('customer.home') }}" class="btn btn-outline-light w-100 rounded-pill mb-2"><i class="fa-solid fa-globe me-2"></i> Customer View</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100 rounded-pill"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <main class="main-wrapper">
        <header class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom bg-white p-3 rounded-4 shadow-sm">
            <div>
                <h4 class="mb-0 fw-bold">@yield('page_header', 'Dashboard')</h4>
                <p class="text-secondary small mb-0">Management Portal for Sip N Bite Café</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i class="fa-solid fa-wifi me-1"></i> Live System Sync</span>
                <div class="fw-semibold"><i class="fa-solid fa-user-shield me-1 text-danger"></i> {{ Auth::user()->name ?? 'Admin' }}</div>
            </div>
        </header>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
