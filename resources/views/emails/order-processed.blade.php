<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .items { border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .item { padding: 10px 0; border-bottom: 1px solid #eee; }
        .total { font-size: 18px; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Order Confirmation</h2>
        </div>

        <p>Hello {{ $order->user->name }},</p>
        <p>Thank you for your order! Here are the details:</p>

        <div class="header">
            <p><strong>Order #{{ $order->id }}</strong></p>
            <p>Order Date: {{ $order->created_at->format('M d, Y') }}</p>
            <p>Status: {{ ucfirst($order->status) }}</p>
            <p>Payment Method: {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}</p>
        </div>

        <h3>Items Ordered:</h3>
        <div class="items">
            @foreach($order->items as $item)
                <div class="item">
                    <strong>{{ $item->product->name }}</strong><br>
                    Quantity: {{ $item->quantity }} × ₱{{ number_format($item->price, 2) }} = <strong>₱{{ number_format($item->price * $item->quantity, 2) }}</strong>
                </div>
            @endforeach
        </div>

        <div class="total">
            Total Amount: ₱{{ number_format($order->total, 2) }}
        </div>

        <h3>Shipping Address:</h3>
        <p>
            {{ $order->shipping_address }}<br>
            Phone: {{ $order->shipping_phone }}
        </p>

        <p>We will notify you once your order has been processed and shipped.</p>
        <p>Thank you for shopping with {{ config('app.name') }}!</p>

        <hr>
        <p style="font-size: 12px; color: #999;">
            This is an automated email. Please do not reply to this address.
        </p>
    </div>
</body>
</html>
