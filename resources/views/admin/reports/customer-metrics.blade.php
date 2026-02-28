@extends('admin.layout')

@section('content')
<h2 class="mb-4">Customer Metrics</h2>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Customers</h6>
                <h2 class="text-primary">{{ $totalCustomers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Active Customers</h6>
                <h2 class="text-success">{{ $activeCustomers }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Total Orders</h6>
                <h2 class="text-info">{{ $totalOrders }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Avg Order Value</h6>
                <h2 class="text-warning">₱{{ number_format($avgOrderValue, 2) }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection
