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
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
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
                            <td>
                                <input type="checkbox" name="selected_items" value="{{ $item->product_id }}" class="form-check-input cart-checkbox" checked>
                            </td>
                            <td style="width:80px;">
                                @if($item->image)
                                    <img src="{{ filter_var($item->image, FILTER_VALIDATE_URL) ? $item->image : asset('storage/'.$item->image) }}" class="img-fluid" alt="{{ $item->name }}" style="height: 60px; object-fit: cover;">
                                @else
                                    <img src="https://via.placeholder.com/80" class="img-fluid" alt="Placeholder">
                                @endif
                            </td>
                            <td>{{ $item->name }}</td>
                            <td>₱{{ number_format($item->price, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('updateCartQuantity', $item->product_id) }}" style="display:inline; max-width: 140px;">
                                    @csrf
                                    <input type="hidden" name="address_id" value="{{ old('address_id') }}">
                                    <input type="hidden" name="shipping_address" value="{{ old('shipping_address') }}">
                                    <input type="hidden" name="shipping_phone" value="{{ old('shipping_phone') }}">
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary qty-minus" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">−</button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->stock }}" class="form-control qty-input text-center" data-max="{{ $item->stock }}" style="padding: 0.25rem; font-size: 0.875rem;">
                                        <button type="button" class="btn btn-outline-secondary qty-plus" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">+</button>
                                        <button type="submit" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">✓</button>
                                    </div>
                                </form>
                            </td>
                            <td>₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('removeItem', $item->product_id) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="address_id" value="{{ old('address_id') }}">
                                    <input type="hidden" name="shipping_address" value="{{ old('shipping_address') }}">
                                    <input type="hidden" name="shipping_phone" value="{{ old('shipping_phone') }}">
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    // Quantity +/- buttons
                    document.querySelectorAll('.qty-input').forEach(input => {
                        const maxStock = parseInt(input.dataset.max);
                        const form = input.closest('form');
                        const minus = form.querySelector('.qty-minus');
                        const plus = form.querySelector('.qty-plus');

                        minus.addEventListener('click', e => {
                            e.preventDefault();
                            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
                        });

                        plus.addEventListener('click', e => {
                            e.preventDefault();
                            if (parseInt(input.value) < maxStock) input.value = parseInt(input.value) + 1;
                        });
                    });

                    // Select all checkbox
                    const selectAllCheckbox = document.getElementById('select-all');
                    const cartCheckboxes = document.querySelectorAll('.cart-checkbox');

                    if (selectAllCheckbox) {
                        selectAllCheckbox.addEventListener('change', function() {
                            cartCheckboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });
                    }

                    // Update select-all when individual checkboxes change
                    cartCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            if (selectAllCheckbox) {
                                selectAllCheckbox.checked = Array.from(cartCheckboxes).every(cb => cb.checked);
                            }
                            recalcCartTotal();
                        });
                    });

                    // Validate at least one item is selected before checkout
                    const checkoutForm = document.querySelector('form[action*="checkout"]');
                    if (checkoutForm) {
                        checkoutForm.addEventListener('submit', function(e) {
                            const checkedItems = Array.from(cartCheckboxes).filter(cb => cb.checked);
                            if (checkedItems.length === 0) {
                                e.preventDefault();
                                alert('Please select at least one item to checkout');
                                return;
                            }

                            // inject hidden inputs for selected items so they are submitted
                            checkedItems.forEach(cb => {
                                const hid = document.createElement('input');
                                hid.type = 'hidden';
                                hid.name = 'selected_items[]';
                                hid.value = cb.value;
                                checkoutForm.appendChild(hid);
                            });
                        });
                    }

                    // recalc total based on selected rows
                    function recalcCartTotal() {
                        let total = 0;
                        document.querySelectorAll('tbody tr').forEach(row => {
                            const checkbox = row.querySelector('.cart-checkbox');
                            if (checkbox && checkbox.checked) {
                                const lineCell = row.querySelector('td:nth-child(6)');
                                if (lineCell) {
                                    const text = lineCell.textContent.replace(/[^0-9\.]/g,'');
                                    total += parseFloat(text) || 0;
                                }
                            }
                        });
                    document.getElementById('cart-total').textContent = total.toFixed(2);
                }

                cartCheckboxes.forEach(cb => cb.addEventListener('change', recalcCartTotal));
                selectAllCheckbox.addEventListener('change', recalcCartTotal);
                // initial calc
                recalcCartTotal();

                // keep address data when submitting update/remove forms
                function attachAddressToForm(form) {
                    const addrSel = document.getElementById('address_id');
                    const addrTxt = document.getElementById('shipping_address');
                    const phoneTxt = document.getElementById('shipping_phone');
                    if (!form) return;
                    [
                        {name:'address_id', value: addrSel?.value},
                        {name:'shipping_address', value: addrTxt?.value},
                        {name:'shipping_phone', value: phoneTxt?.value}
                    ].forEach(data => {
                        if (data.value !== undefined) {
                            let existing = form.querySelector('input[name="'+data.name+'"]');
                            if (existing) existing.value = data.value;
                            else {
                                let hid = document.createElement('input');
                                hid.type = 'hidden';
                                hid.name = data.name;
                                hid.value = data.value;
                                form.appendChild(hid);
                            }
                        }
                    });
                }

                // add listener to quantity and remove forms
                document.querySelectorAll('form[action*="updateCartQuantity"], form[action*="removeItem"]').forEach(frm => {
                    frm.addEventListener('submit', function(e){
                        attachAddressToForm(this);
                    });
                });
            });  // end DOMContentLoaded wrapper
            </script>

            <div class="row mt-3">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Order Summary</h5>
                            <p><strong>Total: ₱<span id="cart-total">{{ number_format($totalPrice, 2) }}</span></strong></p>
                            <form method="POST" action="{{ route('checkout') }}">
                                @csrf
                                <script>
                                    document.addEventListener('DOMContentLoaded', function(){
                                        var sel = document.getElementById('address_id');
                                        if(!sel) return;
                                        // populate fields on load if already selected
                                        if(sel.value) {
                                            var opt = sel.options[sel.selectedIndex];
                                            var addr = opt.getAttribute('data-address');
                                            var phone = opt.getAttribute('data-phone');
                                            if(addr) document.getElementById('shipping_address').value = addr;
                                            if(phone) document.getElementById('shipping_phone').value = phone;
                                        }
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
                                                <option value="{{ $addr->id }}" data-address="{{ htmlentities($addr->address) }}" data-phone="{{ e($addr->phone) }}" {{ old('address_id') == $addr->id ? 'selected' : '' }}>{{ $addr->label ?: 'Address '. $addr->id }}</option>
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
            