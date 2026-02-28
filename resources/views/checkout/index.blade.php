@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Checkout</h1>

    <div class="row">
        <div class="col-md-8">
            <h5>Order Summary</h5>
            <table class="table align-middle">
                <thead><tr><th></th><th>Product</th><th>Qty</th><th>Price</th><th>Line</th></tr></thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td style="width:60px;">
                            @if($item['product']?->image)
                                <img src="{{ Str::startsWith($item['product']->image,'http') ? $item['product']->image : asset('storage/'.$item['product']->image) }}" class="img-fluid" alt="" />
                            @endif
                        </td>
                        <td>{{ $item['product']?->name }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₱{{ number_format($item['product']->price,2) }}</td>
                        <td>₱{{ number_format($item['product']->price * $item['quantity'],2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <div style="background:#fffbf7; padding:20px; border-radius:8px; border:1px solid #e8dfd8;">
                <h5>Total Amount</h5>
                <p style="font-size:2em; font-weight:bold; color:#000;">₱{{ number_format($total,2) }}</p>

                <form method="POST" action="{{ route('checkout') }}" class="mt-4">
                    @csrf

                    @php $user = auth()->user(); @endphp
                    @if($user && $user->addresses()->count() > 0)
                        <div class="mb-3">
                            <label for="address_id" class="form-label">Saved Addresses</label>
                            <select id="address_id" name="address_id" class="form-select">
                                <option value="">Use new address</option>
                                @foreach($user->addresses as $addr)
                                    <option value="{{ $addr->id }}" data-address="{{ htmlentities($addr->address) }}" data-phone="{{ e($addr->phone) }}" {{ old('address_id') == $addr->id ? 'selected' : '' }}>{{ $addr->label ?: 'Address '. $addr->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            var sel = document.getElementById('address_id');
                            if(!sel) return;
                            if(sel.value){
                                var opt = sel.options[sel.selectedIndex];
                                var addr = opt.getAttribute('data-address');
                                var phone = opt.getAttribute('data-phone');
                                if(addr) document.getElementById('shipping_address').value = addr;
                                if(phone) {
                                    var phoneEl = document.getElementById('shipping_phone');
                                    if(phoneEl) phoneEl.value = phone;
                                }
                            }
                            sel.addEventListener('change', function(){
                                var opt = sel.options[sel.selectedIndex];
                                var addr = opt.getAttribute('data-address');
                                var phone = opt.getAttribute('data-phone');
                                if(addr) document.getElementById('shipping_address').value = addr;
                                if(phone) {
                                    var phoneEl = document.getElementById('shipping_phone');
                                    if(phoneEl) phoneEl.value = phone;
                                }
                            });
                        });
                    </script>

                    <!-- make sure there are fields for manual input if user isn't using a saved address -->
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="shipping_phone" class="form-label">Phone</label>
                        <input type="text" name="shipping_phone" id="shipping_phone" class="form-control" value="{{ old('shipping_phone') }}" required>
                        @error('shipping_phone')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <h6>Payment Method</h6
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="online" required>
                            <label class="form-check-label" for="online">
                                <strong>Online Payment</strong><br>
                                <small style="color:#666;">Pay now using bank transfer or online banking</small>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" required>
                            <label class="form-check-label" for="cod">
                                <strong>Cash on Delivery (COD)</strong><br>
                                <small style="color:#666;">Pay when your order arrives</small>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="padding:10px;">
                        <i class="fas fa-lock"></i> Place Order
                    </button>
                </form>

                <p style="margin-top:15px; font-size:0.9em; color:#666; text-align:center;">
                    <i class="fas fa-shield-alt"></i> Your data is secure
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
