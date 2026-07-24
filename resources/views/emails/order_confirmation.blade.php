<!DOCTYPE html>
<html>
<head>
    <style>
        .email-container { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px; }
        .header { background: #f8f8f8; padding: 10px; text-align: center; }
        .item-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .item-table th { text-align: left; border-bottom: 2px solid #eee; padding: 10px; }
        .item-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; text-align: right; padding: 15px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Thank you for your order!</h2>
        </div>
        <p>Hi {{ $name }},</p>
        <p>We've received your order <strong>#{{ $order_no }}</strong> and it is now being processed.</p>

        <table class="item-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Tk{{ number_format($item->price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total Amount: Tk{{ number_format($total, 2) }}
        </div>

        <p>Best regards,<br>Amader Sanitary</p>
    </div>
</body>
</html>