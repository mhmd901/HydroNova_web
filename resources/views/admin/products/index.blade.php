@extends('layouts.admin')

@section('title', 'Manage Products - HydroNova Admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">Products Management</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle"></i> Add New Product
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="table-responsive shadow-sm">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price ($)</th>
                    <th>3D Model</th>
                    <th>Created</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $id => $product)
                    <tr>
                        <td width="80">
                            <img src="{{ $product['image_url'] ?? asset('images/hydronova_logo.png') }}"
                                 alt="Product Image"
                                 class="rounded"
                                 style="width: 70px; height: 70px; object-fit: cover;">
                        </td>
                        <td>{{ $product['name'] ?? '—' }}</td>
                        <td>${{ number_format((float)($product['price'] ?? 0), 2) }}</td>
                        <td>
                            @if (!empty($product['model_url']))
                                <button class="btn btn-sm btn-outline-success shadow-sm"
                                        onclick="init3DViewer(@json(route('stl.show', $id)))"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modelViewerModal"
                                        data-model-url="{{ route('stl.show', $id) }}">
                                    <i class="bi bi-cube"></i> View 3D
                                </button>
                            @else
                                <span class="text-muted small">No model</span>
                            @endif
                        </td>
                        <td>{{ $product['created_at'] ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.products.edit', $id) }}" class="btn btn-sm btn-warning shadow-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger shadow-sm"
                                        onclick="return confirm('Delete this product?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No products found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ✅ Reuse the same modal viewer --}}
@include('components.3dviewer')
@endsection
