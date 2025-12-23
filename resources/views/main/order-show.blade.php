@extends('layouts.app')

@section('title', 'Order Details | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
            <div>
                <h1 class="h4 fw-bold mb-1">Order {{ $order['id'] ?? $order['key'] }}</h1>
                <p class="text-muted mb-0">Placed on {{ $order['created_at'] ?? '' }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @php
                    $statusStyles = [
                        'Pending'    => 'bg-warning text-dark',
                        'Confirmed'  => 'bg-primary',
                        'Processing' => 'bg-info text-dark',
                        'Shipped'    => 'bg-secondary',
                        'Delivered'  => 'bg-success',
                        'Cancelled'  => 'bg-danger',
                    ];
                @endphp
                <span class="badge {{ $statusStyles[$order['status'] ?? 'Pending'] ?? 'bg-secondary' }} px-3 py-2 fs-6">{{ $order['status'] ?? 'Pending' }}</span>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">Back to orders</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Items</h2>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order['items'] ?? [] as $item)
                                        <tr>
                                            <td>{{ $item['name'] ?? $item['product_name'] ?? 'Product' }}</td>
                                            <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                                            <td class="text-end">${{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                                            <td class="text-end">${{ number_format((float)($item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1))), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Shipping details</h2>
                        <p class="mb-1 fw-semibold">{{ $order['full_name'] ?? '' }}</p>
                        <p class="mb-1 text-muted">{{ $order['email'] ?? '' }}</p>
                        <p class="mb-1">
                            @if (!empty($order['phone']))
                                <a href="tel:{{ $order['phone'] }}" class="text-decoration-none">
                                    <i class="bi bi-telephone"></i> {{ $order['phone'] }}
                                </a>
                            @else
                                <span class="text-muted">No phone</span>
                            @endif
                        </p>
                        <p class="mb-1"><i class="bi bi-geo-alt"></i> {{ $order['address'] ?? '' }}</p>
                        @if (!empty($order['city']))
                            <p class="mb-0 text-muted">{{ $order['city'] }}</p>
                        @endif
                        @if (!empty($order['note']))
                            <div class="mt-3">
                                <p class="fw-semibold mb-1">Note</p>
                                <p class="mb-0 text-muted">{{ $order['note'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Totals</h2>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">${{ number_format((float)($order['subtotal'] ?? 0), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Delivery fee</span>
                            <span class="fw-semibold">${{ number_format((float)($order['delivery_fee'] ?? 0), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between fs-5 fw-bold mt-2">
                            <span>Total</span>
                            <span>${{ number_format((float)($order['total'] ?? 0), 2) }}</span>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('order.invoice', $order['key']) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-file-earmark-arrow-down"></i> Download invoice
                            </a>
                            <a href="https://wa.me/96181370450?text={{ urlencode('Hello HydroNova, I need support with order '.($order['id'] ?? $order['key'])) }}" target="_blank" class="btn btn-success">
                                <i class="bi bi-whatsapp"></i> Contact support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
