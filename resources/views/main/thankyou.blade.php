@extends('layouts.app')

@section('title', 'Thank You | HydroNova')

@section('content')
<section class="min-vh-100 d-flex align-items-center" style="background: #e7f3ff;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-4 thankyou-card">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <div class="rounded-circle d-inline-flex justify-content-center align-items-center mb-3"
                                 style="width:80px;height:80px;background:#d4edda;">
                                <i class="bi bi-check2-circle text-success" style="font-size:3rem;"></i>
                            </div>
                            <h1 class="h3 fw-bold text-success">Thank you for your order!</h1>
                            <p class="text-muted mb-0">Your order has been placed successfully.</p>
                        </div>

                        @if ($order)
                            <div class="text-start mx-auto" style="max-width: 420px;">
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Order ID</p>
                                    <p class="fw-semibold">{{ $order['id'] ?? 'N/A' }}</p>
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <p class="mb-1 text-muted">Name</p>
                                        <p class="fw-semibold">{{ $order['name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <p class="mb-1 text-muted">Phone</p>
                                        <p class="fw-semibold">{{ $order['phone'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Total</p>
                                    <p class="fw-semibold">${{ number_format((float)($order['total'] ?? 0), 2) }}</p>
                                </div>
                                <p class="text-muted mb-0">We will contact you soon for delivery confirmation.</p>
                            </div>
                        @else
                            <p class="text-muted">We couldn't locate your recent order. If this persists, please contact us through WhatsApp.</p>
                        @endif

                        <div class="mt-5">
                            <a href="https://wa.me/96181370450?text=Hello%20HydroNova%20I%20need%20support%20about%20my%20order"
                               class="btn btn-lg text-white d-inline-flex align-items-center justify-content-center gap-2"
                               style="background:#25D366;border:none;">
                                <i class="bi bi-whatsapp fs-4"></i>
                                Contact Us on WhatsApp
                            </a>
                        </div>
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

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

