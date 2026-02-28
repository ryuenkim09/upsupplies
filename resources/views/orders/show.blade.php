@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Order #{{ $order->id }}</h1>

    @if($order->status === 'Canceled')
        <div class="alert alert-danger">
            This order has been <strong>canceled</strong>.
            @if($order->payment_method === 'online' && $order->payment_status === 'refunded')
                Your payment has been refunded.
            @endif
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            @unless($order->status === 'Canceled')
                <h5>Order Status Timeline</h5>
                <div style="position:relative; padding:20px 0;">
                    @php
                        $statuses = ['Pending', 'Processing', 'Shipped', 'Completed'];
                        $currentIndex = array_search($order->status, $statuses);
                        $statusColors = [
                            'Pending' => 'warning',
                            'Processing' => 'info',
                            'Shipped' => 'primary',
                            'Completed' => 'success',
                        ];
                    @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        @foreach($statuses as $idx => $status)
                            <div style="flex:1; text-align:center;">
                                @if($idx <= $currentIndex)
                                    <div style="width:40px; height:40px; margin:0 auto 10px; border-radius:50%; background-color:#000; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                                        {{ $idx + 1 }}
                                    </div>
                                    <p style="font-weight:bold;">{{ $status }}</p>
                                @else
                                    <div style="width:40px; height:40px; margin:0 auto 10px; border-radius:50%; background-color:#ddd; color:white; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                                        {{ $idx + 1 }}
                                    </div>
                                    <p>{{ $status }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endunless
        </div>
        <div class="col-md-4">
            <h5>Delivery Information</h5>
            <p><strong>Status:</strong></p>
            @php
                $color = $statusColors[$order->status] ?? 'secondary';
            @endphp
            <p><span class="badge bg-{{ $color }} text-dark" style="padding:8px 15px; font-size:1em;">{{ strtoupper($order->status) }}</span></p>
            <p><strong>Delivery Address:</strong></p>
            <div style="background:#f5f0e8; padding:10px; border-radius:5px; font-size:0.95em;">
                {{ $order->user?->address ?? 'No address provided' }}
            </div>
        </div>
    </div>

    <hr>
    <h5>Order Details</h5>
    <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y \\a\\t h:i A') }}</p>
    <p><strong>Total Amount:</strong> <span class="fw-bold" style="font-size:1.2em;">₱{{ number_format($order->total,2) }}</span></p>
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

    @if($order->status === 'Pending')
        <form method="POST" action="{{ route('orders.cancel', $order) }}">
            @csrf
            <button type="submit" class="btn btn-danger mb-4">Cancel Order</button>
        </form>
    @endif

    <h2 class="mt-4">Items</h2>
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