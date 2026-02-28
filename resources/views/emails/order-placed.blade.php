<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #000; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f5f0e8; }
        .order-summary { background: white; padding: 15px; margin: 15px 0; border: 1px solid #ddd; }
        .items-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .items-table th { background: #f0f0f0; font-weight: bold; }
        .total { font-size: 18px; font-weight: bold; text-align: right; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Order #{{ $order->id }}</p>
        </div>

        <div class="content">
            <p>Hello {{ $order->user->name }},</p>
            <p>Thank you for your order! We've received it and it's being processed.</p>

            <div class="order-summary">
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                <p><strong>Status:</strong> <span style="color: #ff9800;">{{ $order->status }}</span></p>
                <p><strong>Delivery Address:</strong> {{ $order->user->address ?? 'Not provided' }}</p>
            </div>

            <h3>Order Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product?->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->price, 2) }}</td>
                            <td>₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total">
                Order Total: ₱{{ number_format($order->total, 2) }}
            </div>

            <p><strong>Payment Method:</strong> 
                @if($order->payment_method === 'online')
                    Online Payment
                @else
                    Cash on Delivery
                @endif
            </p>

            <p>We'll keep you updated on your order status. You can track your order anytime by logging into your account.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} PawMart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
