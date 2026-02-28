<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #000; color: #fff; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f5f0e8; }
        .status-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #ff9800; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .status-badge { display: inline-block; padding: 8px 15px; background: #ff9800; color: white; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Status Update</h1>
            <p>Order #{{ $order->id }}</p>
        </div>

        <div class="content">
            <p>Hello {{ $order->user->name }},</p>
            <p>Your order status has been updated!</p>

            <div class="status-box">
                <h3>Status Change</h3>
                <p>
                    <strong>From:</strong> <span style="color: #999;">{{ $oldStatus }}</span><br>
                    <strong>To:</strong> <span class="status-badge">{{ $newStatus }}</span>
                </p>
            </div>

            <div class="status-box">
                <h3>Order Information</h3>
                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p><strong>Total Amount:</strong> ₱{{ number_format($order->total, 2) }}</p>
                <p><strong>Current Status:</strong> {{ $order->status }}</p>
            </div>

            @if($newStatus === 'Shipped')
                <p>Great news! Your order is on its way. You'll receive it soon at:</p>
                <p><strong>{{ $order->user->address ?? 'Address not provided' }}</strong></p>
            @elseif($newStatus === 'Completed')
                <p>Your order has been delivered! We hope you enjoy your purchase.</p>
            @endif

            <p>You can view your full order details by logging into your account.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} PawMart. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
