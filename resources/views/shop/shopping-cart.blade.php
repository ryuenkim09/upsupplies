@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Your Cart</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @auth
        @if(count($products) > 0)
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th></th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                        <tr>
                            <td style="width:80px;">
                                @if($item->image)
                                    <img src="{{ filter_var($item->image, FILTER_VALIDATE_URL) ? $item->image : asset('storage/'.$item->image) }}" class="img-fluid" alt="{{ $item->name }}" style="height: 60px; object-fit: cover;">
                                @else
                                    <img src="https://via.placeholder.com/80" class="img-fluid" alt="Placeholder">
                                @endif
                            </td>
                            <td>{{ $item->name }}</td>
                            <td>₱{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                            <td>
                                <a href="{{ route('reduceByOne', $item->product_id) }}" class="btn btn-sm btn-warning">-</a>
                                <a href="{{ route('removeItem', $item->product_id) }}" class="btn btn-sm btn-danger">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Order Summary</h5>
                            <p><strong>Total: ₱{{ number_format($totalPrice, 2) }}</strong></p>
                            <form method="POST" action="{{ route('checkout') }}">
                                @csrf
                                <script>
                                    document.addEventListener('DOMContentLoaded', function(){
                                        var sel = document.getElementById('address_id');
                                        if(!sel) return;
                                        sel.addEventListener('change', function(){
                                            var opt = sel.options[sel.selectedIndex];
                                            var addr = opt.getAttribute('data-address');
                                            var phone = opt.getAttribute('data-phone');
                                            if(addr) document.getElementById('shipping_address').value = addr;
                                            if(phone) document.getElementById('shipping_phone').value = phone;
                                        });
                                    });
                                </script>
                                @php $user = auth()->user(); @endphp
                                @if($user && $user->addresses()->count() > 0)
                                    <div class="mb-2">
                                        <label for="address_id" class="form-label">Saved Addresses</label>
                                        <select id="address_id" name="address_id" class="form-select">
                                            <option value="">Use new address</option>
                                            @foreach($user->addresses as $addr)
                                                <option value="{{ $addr->id }}" data-address="{{ htmlentities($addr->address) }}" data-phone="{{ e($addr->phone) }}">{{ $addr->label ?: 'Address '. $addr->id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                    <div class="mb-2">
                                        <label for="shipping_address" class="form-label">Shipping Address</label>
                                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address') }}</textarea>
                                        @error('shipping_address')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>
                                        <div class="mb-2">
                                            <label for="shipping_phone" class="form-label">Phone</label>
                                            <input type="text" name="shipping_phone" id="shipping_phone" class="form-control" value="{{ old('shipping_phone') }}" required>
                                            @error('shipping_phone')<div class="text-danger small">{{ $message }}</div>@enderror
                                        </div>
                                        @if($user)
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="save_address" id="save_address" value="1">
                                                <label class="form-check-label" for="save_address">Save this address to my profile</label>
                                            </div>
                                        @endif
                                <div class="mb-2">
                                    <label class="form-label">Payment Method</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" {{ old('payment_method', 'cod')=='cod' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="payment_cod">Cash on Delivery (COD)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="payment_method" id="payment_online" value="online" {{ old('payment_method')=='online' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="payment_online">Online Payment</label>
                                        </div>
                                    </div>
                                    @error('payment_method')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" class="btn btn-success w-100">Proceed to Checkout</button>
                            </form>
                            <a href="{{ route('getItems') }}" class="btn btn-secondary w-100 mt-2">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div style="background:#fffbf7; padding:30px; border-radius:8px; text-align:center;">
                <p style="font-size:1.1em; margin-bottom:20px;">Your cart is empty.</p>
                <a href="{{ route('getItems') }}" class="btn btn-primary">Continue Shopping</a>
            </div>
        @endif
    @else
        <div style="background:#fffbf7; padding:30px; border-radius:8px; text-align:center;">
            <p style="font-size:1.1em; margin-bottom:20px;">Please log in to view your cart.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
        </div>
    @endauth
</div>
@endsection
