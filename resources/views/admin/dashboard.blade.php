@extends('layouts.app')

@section('content')
<style>
    .admin-card {
        background: linear-gradient(135deg, #f5f0e8 0%, #fffef9 100%);
        border: 2px solid #e0d5c7;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        color: inherit;
    }

    .admin-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 16px rgba(0,0,0,0.15);
        border-color: #8b7355;
    }

    .admin-card i {
        font-size: 48px;
        color: #000;
        margin-bottom: 15px;
    }

    .admin-card h5 {
        font-weight: 600;
        color: #000;
        margin: 10px 0;
    }

    .admin-card p {
        font-size: 0.9rem;
        color: #666;
        margin: 0;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #000 0%, #333 100%);
        color: white;
        padding: 40px 20px;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .dashboard-header h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
    }

    .dashboard-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
    }

    .admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 25px;
        margin-top: 30px;
    }
</style>

<div class="container mt-5">
    <div class="dashboard-header">
        <h1>🐾 Admin Dashboard</h1>
        <p>Welcome back, <strong>{{ auth()->user()->name }}</strong>! Manage your PawMart store here.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="admin-grid">
        <!-- "dashboard.users" name was previously used; route now named admin.users.index -->
        <a href="{{ route('admin.users.index') }}" class="admin-card">
            <i class="fas fa-users"></i>
            <h5>Manage Users</h5>
            <p>Create, edit & delete users</p>
        </a>

        <a href="{{ route('admin.products.index') }}" class="admin-card">
            <i class="fas fa-box"></i>
            <h5>Manage Products</h5>
            <p>Add & manage product inventory</p>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="admin-card">
            <i class="fas fa-list"></i>
            <h5>Manage Categories</h5>
            <p>Organize product categories</p>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="admin-card">
            <i class="fas fa-shopping-cart"></i>
            <h5>Manage Orders</h5>
            <p>View & process customer orders</p>
        </a>

        <a href="{{ route('admin.reports.sales') }}" class="admin-card">
            <i class="fas fa-chart-bar"></i>
            <h5>System Reports</h5>
            <p>View sales & analytics</p>
        </a>

        <a href="{{ route('admin.reviews.index') }}" class="admin-card" style="background: linear-gradient(135deg, #fff5e6 0%, #fffaf0 100%); border-color: #ffd700;">
            <div style="position: relative; width: 100%;">
                <i class="fas fa-star" style="color: #ffc107;"></i>
                @if ($pendingReviews > 0)
                    <span style="position: absolute; top: 0; right: 30%; background: #dc3545; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold;">{{ $pendingReviews }}</span>
                @endif
            </div>
            <h5>Review Moderation</h5>
            <p>
                @if ($pendingReviews > 0)
                    <span style="color: #dc3545; font-weight: 600;">{{ $pendingReviews }} pending</span>
                @else
                    All approved
                @endif
            </p>
        </a>

        <a href="{{ route('admin.inventory.low-stock') }}" class="admin-card" style="background: linear-gradient(135deg, #ffe6e6 0%, #fff5f5 100%); border-color: #ff6b6b;">
            <div style="position: relative; width: 100%;">
                <i class="fas fa-exclamation-triangle" style="color: #ff6b6b;"></i>
                @if ($lowStockProducts > 0)
                    <span style="position: absolute; top: 0; right: 30%; background: #ff6b6b; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold;">{{ $lowStockProducts }}</span>
                @endif
            </div>
            <h5>Low Stock Alert</h5>
            <p>
                @if ($lowStockProducts > 0)
                    <span style="color: #ff6b6b; font-weight: 600;">{{ $lowStockProducts }} low stock</span>
                @else
                    All stocked
                @endif
            </p>
        </a>
</div>
@endsection
