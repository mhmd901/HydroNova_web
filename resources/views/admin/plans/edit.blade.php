@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="card bg-white text-dark shadow-lg border-0">
    <div class="card-body">
      <h3 class="fw-bold text-dark mb-4"><i class="bi bi-pencil-square"></i> Edit Plan</h3>

      <form action="{{ route('admin.plans.update', $id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @php
          $selectedItems = old('product_items', $plan['product_items'] ?? ($plan['product_ids'] ?? []));
          if (is_object($selectedItems)) {
              $selectedItems = (array) $selectedItems;
          } elseif (!is_array($selectedItems)) {
              $selectedItems = [];
          }

          $selectedValues = array_values($selectedItems);
          $selectedKeys = array_keys($selectedItems);
          $hasBoolMap = !empty($selectedValues)
              && count(array_filter($selectedValues, 'is_bool')) === count($selectedValues);

          if ($hasBoolMap) {
              $selectedItems = array_fill_keys($selectedKeys, 1);
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
          <label class="form-label">Plan Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          @if (!empty($plan['image_path']))
            <div class="mt-2">
              <img src="{{ asset('storage/' . $plan['image_path']) }}" alt="Current image" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
              <div class="text-muted small">Current image</div>
            </div>
          @endif
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
