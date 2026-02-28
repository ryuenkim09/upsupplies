@extends('admin.layout')

@section('content')
<h2 class="mb-4">Inventory Reports</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Products</h6>
                <h2 class="text-primary">{{ $totalProducts }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Low Stock</h6>
                <h2 class="text-warning">{{ $lowStockCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Out of Stock</h6>
                <h2 class="text-danger">{{ $outOfStockCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Inventory Value</h6>
                <h2 class="text-success">₱{{ number_format($totalValue, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-outline-warning w-100 mb-3">
            <i class="fas fa-exclamation-triangle"></i> View Low Stock Products
        </a>
    </div>
</div>
@endsection
