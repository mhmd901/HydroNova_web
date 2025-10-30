@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="card bg-dark text-light shadow-lg border-0">
    <div class="card-body">
      <h3 class="fw-bold text-white mb-4"><i class="bi bi-plus-circle"></i> Add New Plan</h3>

      <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf

        <div class="mb-3">
          <label class="form-label">Plan Name</label>
          <input type="text" name="name" class="form-control bg-dark text-light border-info" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Price ($/month)</label>
          <input type="number" step="0.01" name="price" class="form-control bg-dark text-light border-info" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description (optional)</label>
          <textarea name="description" rows="3" class="form-control bg-dark text-light border-info"></textarea>
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
