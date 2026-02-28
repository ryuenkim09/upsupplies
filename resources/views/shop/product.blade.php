@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ filter_var($product->image, FILTER_VALIDATE_URL) ? $product->image : asset('images/'.$product->image) }}" 
                     class="img-fluid mb-3" alt="{{ $product->name }}">
            @endif
            @if($images && $images->count())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($images as $img)
                        <img src="{{ \Illuminate\Support\Str::startsWith($img->path,'http') ? $img->path : asset('storage/'.$img->path) }}" 
                             class="img-thumbnail" style="max-width:120px;">
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <h2>{{ $product->name }}</h2>
            <p class="text-muted mb-2">₱{{ number_format($product->price,2) }}</p>
            <p>{{ $product->description }}</p>
            <p><small class="badge bg-{{ $product->stock>0 ? 'success' : 'danger' }}">
                {{ $product->stock>0 ? 'In Stock' : 'Out of Stock' }}
            </small></p>
            @auth
                @if($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', ['id' => $product->id]) }}" class="mt-3" id="buyForm">
                        @csrf
                        <div style="max-width:400px;">
                            <div class="input-group mb-3">
                                <button type="button" class="btn btn-outline-secondary qty-minus">−</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control text-center qty-input" data-max="{{ $product->stock }}" placeholder="Qty">
                                <button type="button" class="btn btn-outline-secondary qty-plus">+</button>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="action" value="add" class="btn btn-success flex-grow-1">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
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
                    <button class="btn btn-secondary" disabled>Out of Stock</button>
                @endif
            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary">Login to buy</a>
            @endguest

            @if($averageRating)
                <div class="mt-3">
                    @for($i=1;$i<=5;$i++)
                        @if($i <= round($averageRating))
                            <span style="color:#ffc107;">⭐</span>
                        @else
                            <span style="color:#ddd;">⭐</span>
                        @endif
                    @endfor
                    <span class="small text-muted">({{ number_format($averageRating,1) }} average)</span>
                </div>
            @endif
        </div>
    </div>

    <hr class="my-5">

    {{-- Reviews section --}}
    <div class="reviews-section" id="reviews">
        <h3>Customer Reviews</h3>

        @auth
            @if($userReviewed)
                <p class="text-muted">You have already reviewed this product.</p>
            @else
                <form id="review-form" action="{{ route('product.review.store', $product->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="">Select rating</option>
                            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                            <option value="4">⭐⭐⭐⭐ Good</option>
                            <option value="3">⭐⭐⭐ Average</option>
                            <option value="2">⭐⭐ Poor</option>
                            <option value="1">⭐ Very Poor</option>
                        </select>
                        @error('rating')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment (optional)</label>
                        <textarea name="comment" id="comment" rows="3" class="form-control"></textarea>
                        @error('comment')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            @endif
        @else
            <p><a href="{{ route('login') }}">Log in</a> to post a review.</p>
        @endauth

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <hr>

        @if($reviews->count())
            @foreach($reviews as $review)
                <div class="mb-4 p-3" style="background:#f9f9f9; border-radius:5px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>{{ $review->reviewer_name }}</strong>
                            <div class="mb-1">
                                @for($i=1;$i<=5;$i++)
                                    @if($i <= $review->rating)
                                        <span style="color:#ffc107;">⭐</span>
                                    @else
                                        <span style="color:#ddd;">⭐</span>
                                    @endif
                                @endfor
                            </div>
                            @if($review->comment)
                                <p>{{ $review->comment }}</p>
                            @endif
                            <small class="text-muted">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted">No reviews yet.</p>
        @endif
    </div>
</div>
@endsection
