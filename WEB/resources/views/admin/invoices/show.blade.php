<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} — Sip N Bite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #fff; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body onload="window.print()">
<div class="invoice-box my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold text-danger mb-0">Sip N Bite Café & Bistro</h2>
            <p class="text-secondary small mb-0">108 Gourmet Avenue, Sector 18 | Phone: +91 9876543210</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold mb-0">INVOICE</h4>
            <span class="small text-muted">#{{ $order->order_number }}</span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold mb-1">Billed To:</h6>
            <p class="mb-0"><strong>{{ $order->user ? $order->user->name : 'Customer' }}</strong></p>
            <p class="mb-0 text-secondary small">{{ $order->user ? $order->user->email : '' }}</p>
            <p class="mb-0 text-secondary small">{{ $order->user ? $order->user->phone : '' }}</p>
            <p class="text-secondary small">{{ $order->address ? $order->address->address_line . ', ' . $order->address->city : '' }}</p>
        </div>
        <div class="col-6 text-end">
            <p class="mb-1"><strong>Invoice Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
            <p class="mb-1"><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
            <p class="mb-0"><strong>Payment Status:</strong> <span class="badge bg-success text-uppercase">{{ $order->payment_status }}</span></p>
        </div>
    </div>

    <table class="table table-bordered align-middle mb-4">
        <thead class="table-light">
            <tr>
                <th>Item Description</th>
                <th class="text-center">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row justify-content-end">
        <div class="col-5">
            <div class="d-flex justify-content-between mb-1">
                <span>Subtotal:</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span>Tax (5% GST):</span>
                <span>₹{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Delivery Charge:</span>
                <span>₹{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between border-top pt-2">
                <span class="fw-bold fs-5">Total Paid:</span>
                <span class="fw-bold fs-4 text-danger">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="text-center border-top mt-5 pt-3 text-muted small">
        Thank you for choosing Sip N Bite Café! Have a delicious day.
    </div>
</div>
</body>
</html>
