@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            @php $primary = $product->images->first()?->path ?? $product->image; @endphp
            @if($primary)
                <img src="{{ \Illuminate\Support\Str::startsWith($primary,'http') ? $primary : asset('storage/'.$primary) }}" class="img-fluid" alt="{{ $product->name }}">
            @else
                <img src="https://via.placeholder.com/600?text={{ urlencode($product->name) }}" class="img-fluid" alt="{{ $product->name }}">
            @endif
        </div>
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <p class="text-muted">Category: {{ $product->category?->name }}</p>
            <p>{{ $product->description }}</p>
            <p class="fw-bold fs-4">Price: ₱{{ number_format($product->price,2) }}</p>
            <p>Stock: {{ $product->stock }}</p>
            @auth
            <form method="POST" action="{{ route('cart.add', ['id' => $product->id]) }}" class="mt-3" id="buyForm">
                @csrf
                <div style="max-width:400px;">
                    <div class="input-group mb-3">
                        <button type="button" class="btn btn-outline-secondary qty-minus">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center qty-input" data-max="{{ $product->stock }}" placeholder="Qty">
                        <button type="button" class="btn btn-outline-secondary qty-plus">+</button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="add" class="btn btn-success flex-grow-1">Add to Cart</button>
                        <button type="submit" name="action" value="buyNow" class="btn btn-primary flex-grow-1">Buy Now</button>
                    </div>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.qty-input').forEach(input => {
                        const maxStock = parseInt(input.dataset.max) || 1;
                        const form = input.closest('form');
                        if (!form) return;
                        
                        const minus = form.querySelector('.qty-minus');
                        const plus = form.querySelector('.qty-plus');
                        
                        if (minus) {
                            minus.addEventListener('click', e => {
                                e.preventDefault();
                                const current = parseInt(input.value) || 1;
                                if (current > 1) input.value = current - 1;
                            });
                        }
                        
                        if (plus) {
                            plus.addEventListener('click', e => {
                                e.preventDefault();
                                const current = parseInt(input.value) || 1;
                                if (current < maxStock) input.value = current + 1;
                            });
                        }
                    });
                });
            </script>
            @else
                <p><a href="{{ route('login') }}" class="btn btn-primary">Log in to purchase</a></p>
            @endauth
        </div>
    </div>

    <!-- Reviews Section -->
    @include('products.reviews')
</div>
@endsection