<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    <p>Dear {{ $order->user->name }},</p>
    <p>Thank you for your order. Your order number is {{ $order->id }}.</p>
    <p>Total: ${{ $order->total }}</p>
    <p>We will notify you when your order has been shipped.</p>
    <p>Regards,<br>BMS Team</p>
</body>
</html>
