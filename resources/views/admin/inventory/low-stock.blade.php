@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Low Stock Inventory
            </h1>
            <p class="text-muted">Products below minimum stock threshold</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.dashboard.index') }}" class="btn btn-outline-secondary">← Back to Dashboard</a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stock Status Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Low Stock Items</h6>
                            <h3 class="mb-0 text-warning">{{ $lowStockCount }}</h3>
                        </div>
                        <i class="fas fa-warehouse fa-2x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Out of Stock</h6>
                            <h3 class="mb-0 text-danger">{{ $outOfStockCount }}</h3>
                        </div>
                        <i class="fas fa-ban fa-2x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Critical Stock</h6>
                            <h3 class="mb-0 text-danger">{{ $criticalCount }}</h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light border-top">
                    <tr>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Min. Threshold</th>
                        <th>Status</th>
                        <th>Reorder Level</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                         alt="{{ $product->name }}"
                                         class="rounded"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-decoration-none">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">SKU: <code>{{ $product->sku }}</code></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <h5 class="mb-0">
                                @if ($product->stock == 0)
                                    <span class="text-danger fw-bold">0</span>
                                @elseif ($product->stock <= 5)
                                    <span class="text-danger fw-bold">{{ $product->stock }}</span>
                                @elseif ($product->stock <= $product->low_stock_threshold)
                                    <span class="text-warning fw-bold">{{ $product->stock }}</span>
                                @else
                                    {{ $product->stock }}
                                @endif
                            </h5>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $product->low_stock_threshold }}</span>
                        </td>
                        <td>
                            @if ($product->stock == 0)
                                <span class="badge bg-danger p-2">
                                    <i class="fas fa-circle-xmark"></i> Out of Stock
                                </span>
                            @elseif ($product->stock <= 2)
                                <span class="badge bg-danger p-2">
                                    <i class="fas fa-exclamation-triangle"></i> Critical
                                </span>
                            @else
                                <span class="badge bg-warning text-dark p-2">
                                    <i class="fas fa-exclamation-circle"></i> Low Stock
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="flex-grow-1">
                                    @php
                                        $maxStock = $product->low_stock_threshold * 2;
                                        $percentWidth = (int)($product->stock <= $maxStock ? (($product->stock / $maxStock) * 100) : 100);
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar @if ($product->stock == 0) bg-danger @elseif ($product->stock <= 2) bg-danger @else bg-warning @endif" 
                                             role="progressbar"
                                             aria-valuenow="{{ $product->stock }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="{{ $maxStock }}">
                                        </div>
                                    </div>
                                </div>
                                <small class="text-nowrap">{{ $product->stock }}/{{ $maxStock }}</small>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">{{ $product->updated_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary" title="Edit product">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#thresholdModal{{ $product->id }}"
                                    title="Update threshold">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Threshold Modal -->
                    <div class="modal fade" id="thresholdModal{{ $product->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Update Stock Threshold</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.products.update', $product) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="threshold{{ $product->id }}" class="form-label">Minimum Stock Threshold</label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="threshold{{ $product->id }}" 
                                                       name="low_stock_threshold" 
                                                       value="{{ $product->low_stock_threshold }}" 
                                                       min="1" 
                                                       max="100" 
                                                       required>
                                                <span class="input-group-text">units</span>
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Alert triggered when stock falls below this number
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 opacity-25 text-success"></i>
                            <p class="mb-0">All products are adequately stocked!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.85rem;
    }

    code {
        background-color: #f0f0f0;
        padding: 0.25rem 0.5rem;
        border-radius: 3px;
        font-size: 0.85rem;
    }

    .btn {
        border-radius: 6px;
    }

    .progress {
        border-radius: 4px;
        background-color: #e9ecef;
    }

    .progress-bar {
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        transition: width 0.3s;
    }

    .modal-content {
        border-radius: 8px;
    }
</style>
@endsection
