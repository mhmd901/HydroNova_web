@extends('layouts.app')

@section('title', 'Thank You | HydroNova')

@section('content')
<section class="min-vh-100 d-flex align-items-center" style="background:#f1fbf6;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg rounded-4 thankyou-card overflow-hidden">
                    <div class="card-body p-5 text-center">
                        <div class="success-icon d-inline-flex justify-content-center align-items-center mb-4">
                            <i class="bi bi-check2 text-white"></i>
                        </div>
                        <h1 class="fw-bold text-success mb-2">Thank you for your order!</h1>
                        <p class="text-muted mb-4">Your order has been placed successfully.</p>

                        @if ($order)
                            <div class="success-box mx-auto mb-4 text-start">
                                <div class="d-flex flex-column gap-3">
                                    <div>
                                        <p class="text-uppercase text-muted mb-1 small">Order ID</p>
                                        <p class="fw-semibold fs-5 mb-0">{{ $order['id'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="text-uppercase text-muted mb-1 small">Name</p>
                                            <p class="fw-medium mb-0">{{ $order['full_name'] ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-uppercase text-muted mb-1 small">Phone</p>
                                            <p class="fw-medium mb-0">{{ $order['phone'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-uppercase text-muted mb-1 small">Address</p>
                                        <p class="fw-medium mb-0">{{ $order['address'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="text-uppercase text-muted mb-1 small">Subtotal</p>
                                            <p class="fw-semibold mb-0">${{ number_format((float)($order['subtotal'] ?? 0), 2) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-uppercase text-muted mb-1 small">Total</p>
                                            <p class="fw-semibold mb-0 text-success">${{ number_format((float)($order['total'] ?? 0), 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($order['items']))
                                <div class="table-responsive mb-4">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Price</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order['items'] as $item)
                                                <tr>
                                                    <td>{{ $item['name'] ?? 'Item' }}</td>
                                                    <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                                                    <td class="text-end">${{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                                                    <td class="text-end">${{ number_format((float)($item['subtotal'] ?? 0), 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                                @php
                                    $whatsAppText = urlencode('Hello HydroNova I need support about Order ID ' . ($order['id'] ?? ''));
                                    $invoiceTarget = $order['key'] ?? null;
                                @endphp
                                <a href="https://wa.me/96181370450?text={{ $whatsAppText }}"
                                   class="btn btn-success btn-lg px-4 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm"
                                   style="background:#25D366;border-color:#25D366;">
                                    <span class="fs-4">👉</span>
                                    Message Us on WhatsApp
                                </a>
                                <a href="{{ $invoiceTarget ? route('order.invoice', $invoiceTarget) : '#' }}"
                                   class="btn btn-primary btn-lg px-4 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm"
                                   @if (!$invoiceTarget) aria-disabled="true" @endif>
                                    <i class="bi bi-file-earmark-arrow-down fs-4"></i>
                                    Download Invoice (PDF)
                                </a>
                            </div>
                        @else
                            <p class="text-muted fs-5">We could not find a recent order.</p>
                            <p class="text-muted">If you placed an order recently, you can review it from your orders page.</p>
                            <a href="{{ route('orders.index') }}" class="btn btn-teal mt-3">Go to My Orders</a>
                        @endif

                        @if (session('invoice_error'))
                            <div class="alert alert-warning mt-4 mb-0">
                                {{ session('invoice_error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<style>
    .thankyou-card {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    .success-icon {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 10px 25px rgba(34,197,94,0.35);
        color: #fff;
    }
    .success-icon i {
        font-size: 2.75rem;
    }
    .success-box {
        background: #e8f7ef;
        border-radius: 20px;
        padding: 24px;
    }
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
