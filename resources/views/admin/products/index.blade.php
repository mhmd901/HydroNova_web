@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Product Library</h3>
            <p class="text-muted mb-0">Manage items, upload STL files, and preview models directly from the dashboard.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Product
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Image</th>
                            <th>Name</th>
                            <th style="width: 120px;">Price</th>
                            <th>Description</th>
                            <th style="width: 260px;">3D Preview</th>
                            <th class="text-center" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($products ?? []) as $key => $product)
                            @php
                                $modelPath = $product['model_3d'] ?? null;
                                $modelUrl = $modelPath ? asset('storage/' . $modelPath) : null;
                            @endphp
                            <tr>
                                <td>
                                    <img src="{{ !empty($product['image_path']) ? asset('storage/' . $product['image_path']) : ($product['image_url'] ?? asset('images/hydronova_logo.png')) }}"
                                         class="rounded border" style="width:64px;height:64px;object-fit:cover;" alt="Product image">
                                </td>
                                <td class="fw-semibold">{{ $product['name'] ?? 'Unnamed Product' }}</td>
                                <td>${{ number_format((float)($product['price'] ?? 0), 2) }}</td>
                                <td>
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($product['description'] ?? 'No description provided.', 80) }}</div>
                                </td>
                                <td>
                                    @if ($modelUrl)
                                        <div class="mini-3d-preview mb-2" data-mini-viewer data-model-url="{{ $modelUrl }}">
                                            <canvas></canvas>
                                            <span class="mini-3d-placeholder">Loading STL…</span>
                                        </div>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-dark w-100"
                                                data-open-viewer
                                                data-model-url="{{ $modelUrl }}"
                                                data-model-name="{{ $product['name'] ?? 'Product Model' }}">
                                            <i class="bi bi-cube"></i> Open 3D Viewer
                                        </button>
                                    @else
                                        <span class="badge bg-light text-dark">No STL uploaded</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.products.edit', $key) }}" class="btn btn-sm btn-warning me-2">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $key) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No products found. Click "Add New Product" to create one.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('components.3d-viewer')
@endsection
