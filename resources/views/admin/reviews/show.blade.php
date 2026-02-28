@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Review Details</h1>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">← Back to Reviews</a>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Review Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-1">
                                <i class="fas fa-user-circle text-primary"></i>
                                {{ $review->user->name }}
                            </h5>
                            <small class="text-muted">{{ $review->user->email }}</small>
                        </div>
                        <div class="col-auto">
                            @if ($review->approved)
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning text-dark p-2">
                                    <i class="fas fa-hourglass-half"></i> Pending Approval
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Product Info -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted text-uppercase small mb-2">Product</h6>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                @if ($review->product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $review->product->images->first()->image_path) }}" 
                                         alt="{{ $review->product->name }}"
                                         class="rounded"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <h6 class="mb-1">
                                    <a href="{{ route('product.show', $review->product->id) }}" target="_blank" class="text-decoration-none">
                                        {{ $review->product->name }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    SKU: <code>{{ $review->product->sku }}</code>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted text-uppercase small mb-2">Rating</h6>
                        <div class="d-flex align-items-center">
                            <div class="text-warning" style="font-size: 1.5rem;">
                                @for ($i = 0; $i < (int)$review->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                                @for ($i = (int)$review->rating; $i < 5; $i++)
                                    <i class="far fa-star"></i>
                                @endfor
                            </div>
                            <span class="ms-3 fs-5 fw-bold">{{ (int)$review->rating }}/5</span>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="text-muted text-uppercase small mb-2">Review Comment</h6>
                        <p class="mb-0 lh-lg">{{ $review->review }}</p>
                    </div>

                    <!-- Metadata -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Posted Date</h6>
                            <p class="mb-0">{{ $review->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase small mb-2">Related Order</h6>
                            <p class="mb-0">
                                @if ($review->orderItem && $review->orderItem->order)
                                    <a href="{{ route('admin.orders.show', $review->orderItem->order) }}" class="text-decoration-none">
                                        Order #{{ $review->orderItem->order->id }}
                                    </a>
                                @else
                                    <span class="text-muted">n/a</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    @if (!$review->approved)
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this review? It will be displayed on the product page.')">
                                <i class="fas fa-check-circle"></i> Approve Review
                            </button>
                        </form>
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject and delete this review permanently?')">
                                <i class="fas fa-trash"></i> Reject & Delete
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Revoke approval? This review will be hidden from the product page.')">
                                <i class="fas fa-undo"></i> Revoke Approval
                            </button>
                        </form>
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Delete this review permanently?')">
                                <i class="fas fa-trash"></i> Delete Review
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Summary Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0">Product Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted small text-uppercase">Average Rating</h6>
                        <div class="d-flex align-items-center">
                            <span class="fs-4 fw-bold text-warning me-2">
                                {{ number_format($review->product->reviews()->where('approved', true)->avg('rating') ?? 0, 1) }}
                            </span>
                            <small class="text-muted">/5.0</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted small text-uppercase">Total Reviews</h6>
                        <p class="mb-0">{{ $review->product->reviews()->where('approved', true)->count() }} approved</p>
                        <small class="text-muted">
                            {{ $review->product->reviews()->where('approved', false)->count() }} pending
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
    }

    .card-header {
        border-radius: 8px 8px 0 0;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    code {
        background-color: #f0f0f0;
        padding: 0.25rem 0.5rem;
        border-radius: 3px;
    }

    .btn {
        border-radius: 6px;
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
