@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Orders</h1>
    <table class="table">
        <thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th>Action</th></tr></thead>
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
                <td>{{ $o->id }}</td>
                <td>{{ $o->user?->name }}</td>
                <td>₱{{ $o->total }}</td>
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
                        @if($o->status === 'completed')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-secondary">Pending</span>
                        @endif
                    @endif
                </td>
                <td>{{ $o->created_at->format('M d, Y') }}</td>
                <td><a href="{{ route('admin.orders.show',$o) }}" class="btn btn-sm btn-info">View</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $orders->links() }}
</div>
@endsection