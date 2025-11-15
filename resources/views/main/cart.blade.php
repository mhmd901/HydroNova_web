@extends('layouts.app')

@section('title', 'HydroNova | Cart')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Your Shopping Cart</h1>
                        <p class="text-muted mb-0">Update quantities, remove items, or proceed to checkout when you are ready.</p>
                    </div>
                    <span class="badge bg-secondary fs-6 px-3 py-2">
                        {{ $summary['count'] }} {{ \Illuminate\Support\Str::plural('item', $summary['count']) }}
                    </span>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (empty($cart))
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
                            <h4 class="fw-semibold">Your cart is currently empty</h4>
                            <p class="text-muted mb-4">Browse our catalog and add products to your cart to see them here.</p>
                            <a href="{{ route('main.products') }}" class="btn btn-teal">
                                <i class="bi bi-box-seam me-2"></i>Explore Products
                            </a>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Product</th>
                                            <th scope="col" class="text-center">Price</th>
                                            <th scope="col" class="text-center">Quantity</th>
                                            <th scope="col" class="text-end">Subtotal</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cart as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="ratio ratio-1x1 rounded" style="width:64px; background:#f3f6f9;">
                                                            @if ($item['image'])
                                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="img-fluid rounded">
                                                            @else
                                                                <span class="d-flex align-items-center justify-content-center text-muted">No Image</span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ $item['name'] }}</h6>
                                                            <small class="text-muted">#{{ $item['id'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center fw-semibold">${{ number_format($item['price'], 2) }}</td>
                                                <td>
                                                    <form action="{{ route('cart.update') }}" method="POST" class="d-flex justify-content-center align-items-center gap-2" data-cart-update-form>
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                        <input type="number" name="quantity" min="1" value="{{ $item['quantity'] }}" class="form-control form-control-sm text-center cart-qty-input" style="max-width: 80px;" data-cart-qty-input>
                                                        <button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
                                                    </form>
                                                </td>
                                                <td class="text-end fw-semibold">${{ number_format($item['subtotal'], 2) }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('cart.remove') }}" method="POST" class="d-inline" data-cart-remove-form>
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove item">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 gy-3">
                        <div class="col-md-6">
                            <a href="{{ route('main.products') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                        <div class="col-md-3">
                            <form action="{{ route('cart.clear') }}" method="POST" data-cart-clear-form>
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-x-circle"></i> Clear Cart
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('checkout') }}" class="btn btn-teal w-100">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <p class="text-muted mb-1">Order Summary</p>
                                <h3 class="h4 fw-bold mb-0">${{ number_format($summary['total'], 2) }}</h3>
                            </div>
                            <p class="mb-0 text-muted">Secure checkout with WhatsApp confirmation. You can still cancel before final approval.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
