<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #000; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f5f0e8; }
        .alert-box { background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #e74c3c; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Cancelled</h1>
            <p>Order #{{ $order->id }}</p>
        </div>

        <div class="content">
            <p>Hello {{ $order->user->name }},</p>
            <p>Your order has been cancelled as requested.</p>

            <div class="alert-box">
                <h3>Cancellation Details</h3>
                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p><strong>Order Amount:</strong> ₱{{ number_format($order->total, 2) }}</p>
                <p><strong>Cancelled Date:</strong> {{ now()->format('M d, Y \a\t h:i A') }}</p>
            </div>

            @if($order->payment_method === 'online')
                <div class="alert-box" style="background: #d4edda; border-left-color: #28a745;">
                    <h3>Refund Status</h3>
                    @if($order->payment_status === 'refunded')
                        <p>Your payment has been refunded. The amount ₱{{ number_format($order->total, 2) }} will be credited back to your account within 3-5 business days.</p>
                    @else
                        <p>Since the payment was still pending, no refund is needed.</p>
                    @endif
                </div>
            @else
                <p>As this order was placed with Cash on Delivery, no refund is required.</p>
            @endif

            <p>If you have any questions about this cancellation, please contact our support team.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} PawMart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
