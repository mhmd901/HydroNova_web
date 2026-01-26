@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="card bg-white text-dark shadow-lg border-0">
    <div class="card-body">
      <h3 class="fw-bold text-dark mb-4"><i class="bi bi-plus-circle"></i> Add New Plan</h3>

      <form action="{{ route('admin.plans.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @php
          $selectedItems = old('product_items', []);
          if (is_object($selectedItems)) {
              $selectedItems = (array) $selectedItems;
          } elseif (!is_array($selectedItems)) {
              $selectedItems = [];
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
          <label class="form-label">Plan Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="mb-3">
          <label class="form-label">Included Products (set quantity)</label>
          <div class="border rounded p-2 bg-white" style="max-height: 260px; overflow:auto;">
            @forelse ($products as $productId => $product)
              @php
                $qty = (int) ($selectedItems[$productId] ?? 0);
              @endphp
              <div class="d-flex align-items-center justify-content-between gap-2 py-1">
                <label class="form-label mb-0" for="product-{{ $loop->index }}">
                  {{ $product['name'] ?? 'Unnamed Product' }}
                </label>
                <input class="form-control form-control-sm"
                       style="max-width: 110px;"
                       type="number"
                       min="0"
                       name="product_items[{{ $productId }}]"
                       id="product-{{ $loop->index }}"
                       value="{{ $qty }}">
              </div>
            @empty
              <div class="text-muted small">No products available.</div>
            @endforelse
          </div>
          <div class="form-text">Use 0 to exclude a product.</div>
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
