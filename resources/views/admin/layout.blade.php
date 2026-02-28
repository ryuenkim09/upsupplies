@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UpSupplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #000;
            --primary-brown: #8b7355;
            --primary-beige: #f0ebe5;
        }

        body {
            background-color: var(--primary-dark);
            color: #333;
        }

        .navbar {
            background-color: var(--primary-brown);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--primary-beige) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--primary-beige) !important;
            margin-left: 10px;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .sidebar {
            background-color: var(--primary-brown);
            color: var(--primary-beige);
            min-height: calc(100vh - 60px);
            padding: 20px;
        }

        .sidebar .nav-link {
            color: var(--primary-beige) !important;
            margin: 10px 0;
            border-radius: 4px;
            padding: 10px 15px;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(0,0,0,0.1);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-dark);
        }

        .main-content {
            background-color: var(--primary-beige);
            min-height: calc(100vh - 60px);
            padding: 30px;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-top: 4px solid var(--primary-brown);
        }

        .btn-primary {
            background-color: var(--primary-brown);
            border-color: var(--primary-brown);
        }

        .btn-primary:hover {
            background-color: #6b5344;
            border-color: #6b5344;
        }

        .table {
            background-color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(139, 115, 85, 0.1);
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}">
                <i class="fas fa-paw"></i> UpSupplies Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('getItems') }}">
                            <i class="fas fa-store"></i> Store
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row g-0">
        <div class="col-md-2">
            <div class="sidebar">
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->route()->getName() === 'admin.dashboard.index' ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard.index') }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    <a class="nav-link {{ strpos(request()->route()->getName(), 'admin.products') !== false ? 'active' : '' }}" 
                       href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a class="nav-link {{ strpos(request()->route()->getName(), 'admin.categories') !== false ? 'active' : '' }}" 
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-list"></i> Categories
                    </a>
                    <a class="nav-link {{ strpos(request()->route()->getName(), 'admin.orders') !== false ? 'active' : '' }}" 
                       href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a class="nav-link {{ strpos(request()->route()->getName(), 'admin.users') !== false ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <hr style="border-color: rgba(255,255,255,0.2);">
                    <a class="nav-link {{ strpos(request()->route()->getName(), 'admin.reports') !== false ? 'active' : '' }}" 
                       href="{{ route('admin.reports.sales') }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <a class="nav-link {{ strpos(request()->route()->getName(), 'admin.inventory') !== false ? 'active' : '' }}" 
                       href="{{ route('admin.inventory.summary') }}">
                        <i class="fas fa-warehouse"></i> Inventory
                    </a>
                </nav>
            </div>
        </div>

        <div class="col-md-10">
            <div class="main-content">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
