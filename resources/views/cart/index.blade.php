@extends('layouts.app')
@section('content')
@php
    use Illuminate\Support\Facades\Auth;
@endphp
{{-- @var \Illuminate\Support\Facades\Auth Auth --}}
<div class="container">
    <h1>Your Cart</h1>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if(Auth::check())
        @if(count($items))
            <table class="table align-middle">
                <thead><tr><th></th><th>Product</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead>
                <tbody>
                @foreach($items as $it)
                    <tr>
                        <td style="width:80px;">
                            @if($it['product']->image)
                                <img src="{{ \Illuminate\Support\Str::startsWith($it['product']->image,'http') ? $it['product']->image : asset('storage/'.$it['product']->image) }}" class="img-fluid" alt="" />
                            @else
                                <img src="https://via.placeholder.com/80" class="img-fluid" alt="" />
                            @endif
                        </td>
                        <td>{{ $it['product']->name }}</td>
                        <td style="width:120px;">
                            <form method="POST" action="{{ route('cart.update',$it['id']) }}" class="d-flex">
                                @csrf @method('PATCH')
                                <input type="number" name="quantity" value="{{ $it['quantity'] }}" class="form-control form-control-sm me-1" style="max-width:60px;">
                                <button class="btn btn-sm btn-secondary">OK</button>
                            </form>
                        </td>
                        <td>₱{{ number_format($it['product']->price,2) }}</td>
                        <td>₱{{ number_format($it['product']->price * $it['quantity'],2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.destroy',$it['id']) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p class="fw-bold">Total: ₱{{ number_format($total,2) }}</p>
            <a href="{{ route('checkout.show') }}" class="btn btn-success">Proceed to Checkout</a>
        @else
            <div style="background:#fffbf7; padding:30px; border-radius:8px; text-align:center;">
                <p style="font-size:1.1em; margin-bottom:20px;">Your cart is empty.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a> <!-- redirect to shop home -->
            </div>
        @endif
    @else
        <div style="background:#fffbf7; padding:30px; border-radius:8px; text-align:center;">
            <p style="font-size:1.1em; margin-bottom:20px;">Please log in to view your cart.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
        </div>
    @endif
</div>
@endsection