@extends('layouts.app')

@section('title', 'Products - HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase text-muted small mb-1">HydroNova Catalog</p>
            <h2 class="fw-bold text-teal">Smart Hydroponic Solutions</h2>
            <p class="text-muted mx-auto" style="max-width:720px;">
                Browse our curated lineup of hydroponic systems, each with optional interactive 3D previews to explore modules in full detail before purchasing.
            </p>
        </div>

        <div class="row g-4">
            @forelse (($products ?? []) as $key => $product)
                @php
                    $imagePath = $product['image_path'] ?? null;
                    $image = $imagePath ? asset('storage/' . $imagePath) : ($product['image_url'] ?? asset('images/hydronova_logo.png'));
                    $modelPath = $product['model_3d'] ?? null;
                    $modelUrl = $modelPath ? asset('storage/' . $modelPath) : null;
                @endphp
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <img src="{{ $image }}" alt="{{ $product['name'] ?? 'Product' }}" class="card-img-top" style="height:230px; object-fit:cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-semibold">{{ $product['name'] ?? 'Unnamed Product' }}</h5>
                            <p class="text-muted mb-1">${{ number_format((float)($product['price'] ?? 0), 2) }}</p>
                            <p class="small text-secondary">{{ \Illuminate\Support\Str::limit($product['description'] ?? 'No description provided yet.', 120) }}</p>

                            @if ($modelUrl)
                                <div class="mini-3d-preview mini-3d-preview--public mb-3" data-mini-viewer data-model-url="{{ $modelUrl }}">
                                    <canvas></canvas>
                                    <span class="mini-3d-placeholder">Loading live preview…</span>
                                </div>
                                <button class="btn btn-outline-primary w-100 mb-3"
                                        type="button"
                                        data-open-viewer
                                        data-model-url="{{ $modelUrl }}"
                                        data-model-name="{{ $product['name'] ?? 'Product Model' }}">
                                    <i class="bi bi-cube"></i> Open 3D Viewer
                                </button>
                            @else
                                <div class="alert alert-light border text-center small mb-3">
                                    3D model coming soon.
                                </div>
                            @endif

                            <form action="{{ route('cart.add') }}" method="POST" class="mt-auto" data-add-to-cart-form>
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $key }}">
                                <input type="hidden" name="name" value="{{ $product['name'] ?? 'Unnamed Product' }}">
                                <input type="hidden" name="price" value="{{ $product['price'] ?? 0 }}">
                                <input type="hidden" name="image" value="{{ $image }}">
                                <button class="btn btn-teal w-100 shadow-sm">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">No products available yet. Please check back soon.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@include('components.3d-viewer')
@endsection
