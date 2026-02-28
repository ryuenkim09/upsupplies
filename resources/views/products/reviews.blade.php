<!-- Reviews Section -->
<div class="reviews-section mt-5">
    <h3>Customer Reviews</h3>
    
    @auth
        <!-- Review Form -->
        <div class="review-form-container mb-4">
            @if(auth()->user()->reviewedProduct($product->id))
                <p class="text-muted">You have already reviewed this product.</p>
            @else
                <h5>Share Your Review</h5>
                <form action="{{ route('reviews.store', $product) }}" method="POST">
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
                        <label for="review" class="form-label">Your Review (Optional)</label>
                        <textarea name="review" id="review" class="form-control" rows="4" placeholder="Share your experience with this product..."></textarea>
                        @error('review')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Post Review</button>
                </form>
            @endif
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
    @else
        <p class="mb-4"><a href="{{ route('login') }}">Log in</a> to post a review.</p>
    @endauth

    <!-- Reviews List -->
    <hr>
    
    @if($product->reviews->count() > 0)
        <div class="reviews-list">
            @foreach($product->reviews->sortByDesc('created_at') as $review)
                <div class="review-item mb-4 p-3" style="background: #f9f9f9; border-radius: 5px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $review->user->name }}</h6>
                            <div class="mb-2">
                                @php $rating = (int) $review->rating; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating)
                                        <span style="color: #ffc107;">⭐</span>
                                    @else
                                        <span style="color: #ddd;">⭐</span>
                                    @endif
                                @endfor
                                <small class="text-muted ms-2">{{ $rating }}/5</small>
                            </div>
                            @if($review->review)
                                <p class="mb-2">{{ $review->review }}</p>
                            @endif
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>

                        @auth
                            @if(auth()->id() === $review->user_id)
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-review-{{ $review->id }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this review?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                    @auth
                        @if(auth()->id() === $review->user_id)
                            <div class="collapse mt-3" id="edit-review-{{ $review->id }}">
                                <form action="{{ route('reviews.update', $review) }}" method="POST" class="p-3" style="background: white; border: 1px solid #ddd; border-radius: 5px;">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <select name="rating" class="form-select" required>
                                            @php $currentRating = (int) $review->rating; @endphp
                                            <option value="5" @selected($currentRating == 5)>⭐⭐⭐⭐⭐ Excellent</option>
                                            <option value="4" @selected($currentRating == 4)>⭐⭐⭐⭐ Good</option>
                                            <option value="3" @selected($currentRating == 3)>⭐⭐⭐ Average</option>
                                            <option value="2" @selected($currentRating == 2)>⭐⭐ Poor</option>
                                            <option value="1" @selected($currentRating == 1)>⭐ Very Poor</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Review</label>
                                        <textarea name="review" class="form-control" rows="3">{{ $review->review }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-sm">Update Review</button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
    @endif
</div>
