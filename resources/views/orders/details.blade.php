@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order #{{ $order->id }}</h2>
        <a href="{{ route('orderHistory') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</p>
                    @if(!empty($order->shipping_address))
                        <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
                    @endif
                    @if(!empty($order->shipping_phone))
                        <p><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
                    @endif
                    @if(!empty($order->payment_method))
                        <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                    @endif
                    @if(!empty($order->payment_status))
                        <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                    @endif
                    <p><strong>Order Status:</strong> 
                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p><strong>Order Total:</strong> <h4 class="text-success d-inline">₱{{ number_format($order->total, 2) }}</h4></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Items Ordered</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    @if($order->status === 'completed')
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(!empty($item->image))
                                                    <img src="{{ asset('images/' . $item->image) }}" alt="{{ $item->name }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                                @endif
                                                {{ $item->name }}
                                            </div>
                                        </td>
                                        <td>₱{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><strong>₱{{ number_format($item->price * $item->quantity, 2) }}</strong></td>
                                        @if($order->status === 'completed')
                                            <td>
                                                @php
                                                    $hasReviewed = \Illuminate\Support\Facades\DB::table('reviews')
                                                        ->where('product_id', $item->product_id)
                                                        ->where('user_id', auth()->id())
                                                        ->exists();
                                                @endphp
                                                @if(!$hasReviewed)
                                                    <a href="{{ route('product.show', $item->product_id) }}#review-form" class="btn btn-sm btn-outline-primary">Write Review</a>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th><strong>₱{{ number_format($order->total, 2) }}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">If you have any questions about your order, please contact us.</p>
                    <a href="mailto:support@upsupplies.com" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="fas fa-envelope"></i> Email Support
                    </a>
                    <a href="{{ route('getItems') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
