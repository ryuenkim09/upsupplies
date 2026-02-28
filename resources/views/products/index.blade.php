@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="mb-4">Products</h1>
    <div class="product-header mb-4 text-center">
        <h2>Welcome to {{ config('app.name','PawMart') }}</h2>
        <p class="lead">Browse our catalog and find everything your furry friend needs.</p>
    </div>
    <style>
        .product-header {
            background: #000;
            color: #fff;
            padding: 60px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
        }
        .form-control, .form-select {
            background-color: #fffef9;
            border: 1px solid #e0d5c7;
        }
        .btn-primary {
            background-color: #000;
            border-color: #000;
        }
        .btn-primary:hover {
            background-color: #8b7355;
            border-color: #8b7355;
        }
        /* pagination styling */
        .pagination .page-link {
            background-color: #f5f0e8;
            color: #000;
            border: 1px solid #e0d5c7;
        }
        .pagination .page-item.active .page-link {
            background-color: #000;
            color: #fff;
            border-color: #000;
        }
        .pagination .page-link:hover {
            background-color: #8b7355;
            color: #fff;
        }
        .pagination .page-link i {
            font-size: 1rem;
            vertical-align: middle;
        }
        /* ensure even disabled previous/next match */
        .pagination .page-item.disabled .page-link {
            background-color: transparent !important;
            border: none !important;
            color: #000 !important;
        }
        /* make prev/next arrows minimal */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            background-color: transparent !important;
            border: none !important;
            padding: 0.1rem 0.3rem !important;
            font-size: 1.25rem;
            line-height: 1;
        }
        .pagination .page-item:first-child .page-link:hover,
        .pagination .page-item:last-child .page-link:hover {
            background-color: transparent !important;
            color: #8b7355 !important;
        }
    </style>
    <form method="GET" class="row g-2 mb-4">
        <div class="col-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="form-control" />
        </div>
        <div class="col-auto">
            <select name="category" class="form-select">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="row">
        @forelse($products as $p)
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card product-card h-100 shadow-sm">
                    @php $thumb = $p->images->first()?->path ?? $p->image; @endphp
                    @if($thumb)
                        <img src="{{ \Illuminate\Support\Str::startsWith($thumb,'http') ? $thumb : asset('storage/'.$thumb) }}" class="card-img-top" alt="{{ $p->name }}">
                    @else
                        <img src="https://via.placeholder.com/300?text={{ urlencode($p->name) }}" class="card-img-top" alt="{{ $p->name }}">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $p->name }}</h5>
                        <p class="card-text text-muted mb-2">{{ \Illuminate\Support\Str::limit($p->description, 60) }}</p>
                        <p class="card-text fw-bold">
                            ₱{{ number_format($p->price,2) }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('products.show',$p) }}" class="btn btn-outline-primary btn-sm">View</a>
                            @auth
                                <form method="POST" action="{{ route('cart.add') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn btn-success btn-sm">Add to Cart</button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>No products found.</p>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">
        {{ $products->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection