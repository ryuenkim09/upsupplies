@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Order #{{ $order->id }}</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Customer Information</h5>
            <p><strong>Name:</strong> {{ $order->user?->name }}</p>
            <p><strong>Email:</strong> {{ $order->user?->email }}</p>
            <p><strong>Phone:</strong> {{ $order->shipping_phone ?? $order->user?->phone ?? 'N/A' }}</p>
            <p><strong>Delivery Address:</strong></p>
            <div style="background:#f5f0e8; padding:10px; border-radius:5px;">
                {{ $order->shipping_address ?? $order->user?->address ?? 'No address provided' }}
            </div>
        </div>
        <div class="col-md-6">
            <h5>Order Details</h5>
            <p><strong>Order ID:</strong> {{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
            <p><strong>Total:</strong> <span class="fw-bold">₱{{ number_format($order->total,2) }}</span></p>
            <p><strong>Status:</strong>
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ];
                    $color = $statusColors[$order->status] ?? 'secondary';
                @endphp
                <span class="badge bg-{{ $color }} text-dark">{{ strtoupper($order->status) }}</span>
            </p>
            <p><strong>Payment Method:</strong> 
                @if($order->payment_method === 'online')
                    <span class="badge bg-info">Online Payment</span>
                @else
                    <span class="badge bg-warning">Cash on Delivery</span>
                @endif
            </p>
            <p><strong>Payment Status:</strong>
                @if($order->payment_method === 'cod')
                    @if($order->status === 'Completed')
                        <span class="badge bg-success">Paid on Delivery</span>
                    @else
                        <span class="badge bg-secondary">Pending Payment on Delivery</span>
                    @endif
                @else
                    @if($order->payment_status === 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($order->payment_status === 'refunded')
                        <span class="badge bg-danger">Refunded</span>
                    @else
                        <span class="badge bg-warning">Waiting Payment</span>
                    @endif
                @endif
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="mb-4">
        @csrf
        @method('PUT')
        <div class="input-group" style="max-width:300px;">
            <label class="input-group-text">Update Status:</label>
            <select name="status" class="form-select">
                <option value="pending" {{ $order->status=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ $order->status=='processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ $order->status=='shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="completed" {{ $order->status=='completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $order->status=='cancelled' ? 'selected' : '' }}>Canceled</option>
            </select>
            <button class="btn btn-primary">Update</button>
        </div>
        @error('status')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </form>

    <h2>Items</h2>
    <table class="table align-middle">
        <thead><tr><th></th><th>Product</th><th>Qty</th><th>Price</th><th>Line</th></tr></thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td style="width:60px;">
                    @if($item->product?->image)
                        <img src="{{ Str::startsWith($item->product->image,'http') ? $item->product->image : asset('storage/'.$item->product->image) }}" class="img-fluid" alt="" />
                    @endif
                </td>
                <td>{{ $item->product?->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₱{{ number_format($item->price,2) }}</td>
                <td>₱{{ number_format($item->price * $item->quantity,2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection