@extends('layouts.app')

@section('title', 'HydroNova | Checkout')

@section('content')
@php
    $deliveryFee = $deliveryFee ?? config('services.orders.delivery_fee', 0);
    $grandTotal = $grandTotal ?? ($summary['total'] + $deliveryFee);
@endphp

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-12">
                <h1 class="h3 fw-bold mb-2">Secure Checkout</h1>
                <p class="text-muted mb-4">Share your delivery information and confirm your purchase. Your order will be saved to your account so you can track it.</p>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 fw-semibold mb-3">Customer Details</h2>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('checkout.submit') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                       value="{{ old('full_name', $profile['full_name'] ?? ($customer['full_name'] ?? '')) }}" placeholder="John Doe" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" placeholder="+961 70 000 000" required>
                                <div class="form-text">We normalize Lebanese numbers automatically. Please enter a reachable mobile number.</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                                       value="{{ old('city', $profile['city'] ?? '') }}" placeholder="Beirut">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Delivery Address</label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" required>{{ old('address', $profile['address'] ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Additional Notes (optional)</label>
                                <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Entrance details, preferred time, etc.">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="save_to_profile" id="save_to_profile" value="1" {{ old('save_to_profile', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="save_to_profile">
                                        Save this information to my profile
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Cart
                                </a>
                                <button type="submit" class="btn btn-teal px-4">
                                    Confirm 
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h5 fw-semibold mb-3">Order Summary</h2>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach ($cart as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="fw-semibold mb-0">{{ $item['name'] }}</p>
                                        <small class="text-muted">{{ $item['quantity'] }} &times; ${{ number_format($item['price'], 2) }}</small>
                                    </div>
                                    <span class="fw-bold">${{ number_format($item['subtotal'], 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">${{ number_format($summary['total'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Delivery Fee</span>
                            <span class="fw-semibold">${{ number_format($deliveryFee, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="text-muted">Total</span>
                            <span class="fs-4 fw-bold">${{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <hr>
                        <p class="small text-muted mb-0">
                            After you place the order, you can download the invoice and follow progress from the My Orders page.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    (function () {
        const cartItems = @json($cart);
        try {
            localStorage.setItem('hydronova_cart', JSON.stringify(cartItems));
        } catch (error) {
            console.warn('Unable to persist cart in localStorage', error);
        }
        const count = {{ $summary['count'] }};
        document.querySelectorAll('[data-cart-count]').forEach(function (badge) {
            badge.textContent = count;
        });
    })();
</script>
@endsection
