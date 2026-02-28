@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('User Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h2>Welcome, {{ auth()->user()->name }}!</h2>
                    <p>You are logged in as a <strong>User</strong>.</p>
                    
                    <div class="mt-4">
                        <h4>Your Recent Orders</h4>
                        @php $recent = auth()->user()->orders()->latest()->take(3)->get(); @endphp
                        @if($recent->count())
                        <ul class="list-group">
                            @foreach($recent as $order)
                                <li class="list-group-item">
                                    <a href="{{ route('orders.show',$order) }}">
                                        Order #{{ $order->id }} - {{ $order->status }} - {{ $order->created_at->format('Y-m-d') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @else
                            <p>You have not placed any orders yet.</p>
                        @endif
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">View all orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
