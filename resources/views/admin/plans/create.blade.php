@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="card bg-white text-dark shadow-lg border-0">
    <div class="card-body">
      <h3 class="fw-bold text-dark mb-4"><i class="bi bi-plus-circle"></i> Add New Plan</h3>

      <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf

        @php
          $selectedProducts = old('product_ids', []);
          if (!is_array($selectedProducts)) {
              $selectedProducts = [];
          }
        @endphp

        <div class="mb-3">
          <label class="form-label">Plan Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price ($/month)</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description (optional)</label>
          <textarea name="description" rows="3" class="form-control"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Included Products</label>
          <div class="border rounded p-2 bg-white" style="max-height: 220px; overflow:auto;">
            @forelse ($products as $productId => $product)
              <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="product_ids[]"
                       value="{{ $productId }}"
                       id="product-{{ $loop->index }}"
                       @checked(in_array($productId, $selectedProducts, true))>
                <label class="form-check-label" for="product-{{ $loop->index }}">
                  {{ $product['name'] ?? 'Unnamed Product' }}
                </label>
              </div>
            @empty
              <div class="text-muted small">No products available.</div>
            @endforelse
          </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
          </a>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Save Plan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
