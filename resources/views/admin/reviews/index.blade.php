@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">Review Moderation</h1>
            <p class="text-muted">Manage and approve customer reviews</p>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Reviews</h6>
                            <h3 class="mb-0">{{ $totalReviews }}</h3>
                        </div>
                        <i class="fas fa-star fa-2x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Approval</h6>
                            <h3 class="mb-0 text-danger">{{ $pendingCount }}</h3>
                        </div>
                        <i class="fas fa-hourglass-half fa-2x text-danger opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Approved</h6>
                            <h3 class="mb-0 text-success">{{ $approvedCount }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Approval Rate</h6>
                            <h3 class="mb-0">{{ $totalReviews > 0 ? round(($approvedCount / $totalReviews) * 100) : 0 }}%</h3>
                        </div>
                        <i class="fas fa-chart-pie fa-2x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary {{ !request('filter') ? 'active' : '' }}">
                    All Reviews
                </a>
                <a href="{{ route('admin.reviews.index', ['filter' => 'pending']) }}" class="btn btn-outline-danger {{ request('filter') === 'pending' ? 'active' : '' }}">
                    <i class="fas fa-hourglass-half"></i> Pending ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.reviews.index', ['filter' => 'approved']) }}" class="btn btn-outline-success {{ request('filter') === 'approved' ? 'active' : '' }}">
                    <i class="fas fa-check-circle"></i> Approved ({{ $approvedCount }})
                </a>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light border-top">
                    <tr>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                    <tr>
                        <td>
                            <strong>{{ $review->user->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $review->user->email }}</small>
                        </td>
                        <td>
                            <a href="{{ route('product.show', $review->product->id) }}" target="_blank" class="text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($review->product->name, 30) }}
                            </a>
                        </td>
                        <td>
                            <div class="text-warning">
                                @for ($i = 0; $i < (int)$review->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                                @for ($i = (int)$review->rating; $i < 5; $i++)
                                    <i class="far fa-star"></i>
                                @endfor
                            </div>
                            <small class="text-muted">{{ (int)$review->rating }}/5</small>
                        </td>
                        <td>
                            <div class="text-truncate" title="{{ $review->review }}">
                                {{ \Illuminate\Support\Str::limit($review->review, 50) }}
                            </div>
                        </td>
                        <td>
                            @if ($review->approved)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-half"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $review->created_at->format('M d, Y') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-primary" title="View details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if (!$review->approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" title="Approve review" onclick="return confirm('Approve this review?')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject review" onclick="return confirm('Reject and delete this review?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-warning" title="Revoke approval" onclick="return confirm('Revoke approval for this review?')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">No reviews found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>
</div>

<style>
    .btn-group .btn {
        border-radius: 6px;
        margin-right: 8px;
    }

    .btn-group .btn:not(:last-child) {
        border-right: none;
    }

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
        padding: 0.5rem 0.75rem;
    }
</style>
@endsection
