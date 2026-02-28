@extends('layouts.app')
@section('content')
<div class="container">
    <h1 class="mb-4">Shop</h1>
    <div class="product-header mb-4 text-center" style="background: #000; color: #fff; padding: 60px 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2>Welcome to {{ config('app.name','UpSupplies') }}</h2>
        <p class="lead">Browse our catalog of quality products.</p>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4" style="background-color: #f0ebe5;">
        <div class="card-body">
            <form action="{{ route('getItems') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" placeholder="Search products..." 
                           value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-4">
                    <select class="form-control" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Links for Categories -->
    @if($categories->count() > 0)
        <div class="mb-4">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('getItems') }}" class="badge bg-light text-dark p-2 text-decoration-none {{ !$selectedCategory ? 'bg-primary text-white' : '' }}">
                    All Products
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('getItems', ['category' => $category->id]) }}" 
                       class="badge bg-light text-dark p-2 text-decoration-none {{ $selectedCategory == $category->id ? 'bg-primary text-white' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Products Grid -->
    <div class="row">
        @forelse($items as $item)
            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    @if($item->image)
                        <img src="{{ filter_var($item->image, FILTER_VALIDATE_URL) ? $item->image : asset('images/'.$item->image) }}" 
                             class="card-img-top" alt="{{ $item->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/300?text={{ urlencode($item->name ?? 'Product') }}" 
                             class="card-img-top" alt="Placeholder" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="{{ route('product.show', $item->id) }}" class="text-decoration-none text-dark">
                                {{ $item->name ?? 'Product' }}
                            </a>
                        </h5>
                        <p class="card-text text-muted mb-2" style="font-size: 0.9rem;">{{ \Illuminate\Support\Str::limit($item->description ?? '', 60) }}</p>
                        <p class="card-text fw-bold" style="color: #8b7355;">
                            ₱{{ number_format($item->price ?? 0, 2) }}
                        </p>
                        @if(isset($item->avg_rating) && $item->avg_rating)
                            <p class="card-text mb-1">
                                @for($i=1;$i<=5;$i++)
                                    @if($i <= round($item->avg_rating))
                                        <span style="color:#ffc107;">⭐</span>
                                    @else
                                        <span style="color:#ddd;">⭐</span>
                                    @endif
                                @endfor
                                <small class="text-muted">({{ number_format($item->avg_rating,1) }})</small>
                            </p>
                        @endif
                        <p class="card-text">
                            <small class="badge bg-{{ $item->stock > 0 ? 'success' : 'danger' }}">
                                {{ $item->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                            </small>
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('product.show', $item->id) }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">View Details</a>
                            @auth
                                @if($item->stock > 0)
                                    <a href="{{ route('addToCart', $item->id) }}" class="btn btn-sm w-100" 
                                       style="background-color: #8b7355; color: #fff;">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary w-100" disabled>Out of Stock</button>
                                @endif
                            @endauth
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100">Login to Buy</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p class="text-muted">No products found matching your search criteria.</p>
                <a href="{{ route('getItems') }}" class="btn btn-outline-primary mt-3">Clear Filters</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($items, 'links'))
        <div class="d-flex justify-content-center mt-5 mb-5">
            {{-- enforce bootstrap pagination here as well --}}
            {{ $items->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
