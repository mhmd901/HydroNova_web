@extends('layouts.app')

@section('title', 'Products - HydroNova')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-5 fw-bold text-teal">Our Products</h2>

    <div class="row g-4">
        @forelse ($products as $id => $product)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <img src="{{ $product['image_url'] ?? asset('images/hydronova_logo.png') }}" 
                         class="card-img-top" 
                         alt="{{ $product['name'] ?? 'Product' }}" 
                         style="object-fit: cover; height: 220px;">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">{{ $product['name'] ?? 'Unnamed Product' }}</h5>
                        <p class="text-muted mb-1">${{ number_format((float)($product['price'] ?? 0), 2) }}</p>
                        <p class="small text-secondary">{{ \Illuminate\Support\Str::limit($product['description'] ?? '', 120) }}</p>

                        <form action="{{ route('cart.add') }}" method="POST" class="mt-3" data-add-to-cart-form>
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $id }}">
                            <input type="hidden" name="name" value="{{ $product['name'] ?? 'Unnamed Product' }}">
                            <input type="hidden" name="price" value="{{ $product['price'] ?? 0 }}">
                            <input type="hidden" name="image" value="{{ $product['image_url'] ?? asset('images/hydronova_logo.png') }}">
                            <button class="btn btn-teal w-100 shadow-sm">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>

                        @if (!empty($product['model_url']))
                            <button class="btn btn-outline-primary w-100 mt-2 shadow-sm"
                                    onclick="init3DViewer(@json(route('stl.show', $id)))"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modelViewerModal"
                                    data-model-url="{{ route('stl.show', $id) }}">
                                <i class="bi bi-cube"></i> View in 3D
                            </button>
                        @else
                            <button class="btn btn-secondary w-100 mt-2" disabled>
                                <i class="bi bi-ban"></i> No 3D Model
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-muted">No products available yet.</p>
        @endforelse
    </div>
</div>

{{-- Shared 3D viewer modal and scripts --}}
@include('components.3dviewer')

@endsection
