@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="card bg-dark text-light shadow-lg border-0">
    <div class="card-body">
      <h3 class="fw-bold text-white mb-4"><i class="bi bi-pencil-square"></i> Edit Plan</h3>

      <form action="{{ route('admin.plans.update', $id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Plan Name</label>
          <input type="text" name="name" class="form-control bg-dark text-light border-info"
                 value="{{ $plan['name'] ?? '' }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price ($/month)</label>
          <input type="number" step="0.01" name="price" class="form-control bg-dark text-light border-info"
                 value="{{ $plan['price'] ?? '' }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" rows="3" class="form-control bg-dark text-light border-info">{{ $plan['description'] ?? '' }}</textarea>
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
