<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HydroNova Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #0f172a;
            margin: 0;
            padding: 30px;
            background: #ffffff;
        }
        .header,
        .footer {
            text-align: center;
        }
        .logo {
            height: 48px;
            margin-bottom: 8px;
        }
        h1 {
            margin: 5px 0 0;
            font-size: 20px;
            color: #0f6efd;
        }
        .meta,
        .items,
        .totals {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        .meta td {
            padding: 3px 0;
            vertical-align: top;
        }
        .items th,
        .items td {
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: left;
        }
        .items th {
            background: #f8fafc;
        }
        .totals td {
            padding: 4px 0;
        }
        .totals td:last-child {
            text-align: right;
            font-weight: 600;
        }
        .footer {
            margin-top: 25px;
            font-size: 11px;
            color: #475569;
        }
    </style>
</head>
<body>
    @php
        $orderId = $order['id'] ?? 'N/A';
        $items = $order['items'] ?? [];
        $subtotal = number_format((float)($order['subtotal'] ?? 0), 2);
        $delivery = number_format((float)($order['delivery_fee'] ?? 0), 2);
        $total = number_format((float)($order['total'] ?? 0), 2);
        $logoPath = public_path('images/hydronova_logo.png');
    @endphp

    <div class="header">
        @if (file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="HydroNova" class="logo">
        @endif
        <h1>HydroNova Invoice</h1>
        <p>Order Summary</p>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Order ID:</strong> {{ $orderId }}</td>
            <td><strong>Date:</strong> {{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('F j, Y g:i A') : now()->format('F j, Y g:i A') }}</td>
        </tr>
        <tr>
            <td><strong>Customer:</strong> {{ $order['full_name'] ?? $order['name'] ?? 'N/A' }}</td>
            <td><strong>Phone:</strong> {{ $order['phone'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Address:</strong> {{ $order['address'] ?? 'N/A' }} @if (!empty($order['city'])) ({{ $order['city'] }}) @endif</td>
        </tr>
        @if (!empty($order['note']))
            <tr>
                <td colspan="2"><strong>Note:</strong> {{ $order['note'] }}</td>
            </tr>
        @endif
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 45%;">Item</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item['name'] ?? 'Item' }}</td>
                    <td>${{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                    <td>{{ $item['quantity'] ?? 1 }}</td>
                    <td>${{ number_format((float)($item['subtotal'] ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No items available for this order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td>${{ $subtotal }}</td>
        </tr>
        <tr>
            <td>Delivery Fee</td>
            <td>${{ $delivery }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>${{ $total }}</td>
        </tr>
    </table>

    <div class="footer">
        Thank you for trusting HydroNova. Keep this invoice for your records.
    </div>
</body>
</html>
