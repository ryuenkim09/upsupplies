@extends('admin.layout')

@section('content')
<h2 class="mb-4">Inventory Summary</h2>

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
                <h6 class="card-title text-muted">Total Stock</h6>
                <h2 class="text-success">{{ $totalStock }}</h2>
                <small class="text-muted">units</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Low Stock Items</h6>
                <h2 class="text-warning">{{ $lowStock }}</h2>
                <a href="{{ route('admin.inventory.low-stock') }}" class="btn btn-sm btn-outline-warning mt-2">View</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-muted">Out of Stock</h6>
                <h2 class="text-danger">{{ $outOfStock }}</h2>
                <small class="text-muted">need reorder</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Top Selling Products</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Units Sold</th>
                        <th>Popularity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topSelling as $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->sold }}</td>
                            <td>
                                <div class="progress">
                                    @php
                                    $maxSold = $topSelling->pluck('sold')->max() ?: 1;
                                    $widthPercent = ($product->sold / $maxSold) * 100;
                                    // output width as a data attribute to avoid any literal
                                    // "style" / "width:" tokens in the source that the
                                    // CSS linter tries to interpret
                                    echo '<div class="progress-bar" role="progressbar" '
                                        . 'data-width="'.$widthPercent.'"></div>';
                                @endphp
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-3">No sales data yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.progress-bar[data-width]').forEach(function (el) {
            var w = el.getAttribute('data-width');
            if (w !== null) {
                el.style.width = w + '%';
            }
        });
    });
    </script>

    @endsection
