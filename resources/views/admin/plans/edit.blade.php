@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="card bg-white text-dark shadow-lg border-0">
    <div class="card-body">
      <h3 class="fw-bold text-dark mb-4"><i class="bi bi-pencil-square"></i> Edit Plan</h3>

      <form action="{{ route('admin.plans.update', $id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
          $selectedProducts = old('product_ids', $plan['product_ids'] ?? []);
          if (is_object($selectedProducts)) {
              $selectedProducts = (array) $selectedProducts;
          } elseif (!is_array($selectedProducts)) {
              $selectedProducts = [];
          }

          $selectedValues = array_values($selectedProducts);
          $selectedKeys = array_keys($selectedProducts);
          $selectedProducts = [];
          if (!empty($selectedValues) && count(array_filter($selectedValues, 'is_bool')) === count($selectedValues)) {
              $selectedProducts = $selectedKeys;
          } else {
              $selectedProducts = $selectedValues;
          }
        @endphp

        <div class="mb-3">
          <label class="form-label">Plan Name</label>
          <input type="text" name="name" class="form-control"
                 value="{{ $plan['name'] ?? '' }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price ($/month)</label>
          <input type="number" step="0.01" name="price" class="form-control"
                 value="{{ $plan['price'] ?? '' }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" rows="3" class="form-control">{{ $plan['description'] ?? '' }}</textarea>
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
            <i class="bi bi-arrow-left"></i> Cancel
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check2-circle"></i> Update Plan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
