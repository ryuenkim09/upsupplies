@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Your Orders</h1>
    <table class="table">
        <thead><tr><th>Order ID</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
        @foreach($orders as $o)
            @php
                $statusColors = [
                    'Pending' => 'warning',
                    'Processing' => 'info',
                    'Shipped' => 'primary',
                    'Completed' => 'success',
                    'Canceled' => 'danger',
                ];
                $color = $statusColors[$o->status] ?? 'secondary';
            @endphp
            <tr>
                <td>#{{ $o->id }}</td>
                <td>₱{{ number_format($o->total,2) }}</td>
                <td><span class="badge bg-{{ $color }}">{{ $o->status }}</span></td>
                <td>
                    @if($o->payment_method === 'online')
                        <small>Online</small><br>
                        @if($o->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($o->payment_status === 'refunded')
                            <span class="badge bg-danger">Refunded</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    @else
                        <small>COD</small><br>
                        @if($o->status === 'Completed')
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    @endif
                </td>
                <td>{{ $o->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('orders.show',$o) }}" class="btn btn-sm btn-info">Track</a>
                    @if($o->status === 'Pending')
                        <form action="{{ route('orders.cancel', $o) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-danger">Cancel</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $orders->links() }}
</div>
@endsection