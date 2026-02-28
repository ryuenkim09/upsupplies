@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                                <td><strong>₱{{ number_format($order->total, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orderDetails', $order->id) }}" class="btn btn-sm btn-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">You haven't placed any orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders instanceof \Illuminate\Pagination\Paginator)
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('getItems') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Continue Shopping
        </a>
    </div>
</div>
@endsection
