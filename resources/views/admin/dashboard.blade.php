@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
  <h2 class="fw-bold mb-3"><i class="bi bi-house-door text-info"></i> Dashboard</h2>
  <p class="text-secondary mb-4">Monitor products, plans, and settings in one place.</p>

  {{-- Stats --}}
  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <h5>Total Products</h5>
        <div class="stat-number">{{ isset($products) ? count($products) : 0 }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <h5>Total Plans</h5>
        <div class="stat-number">{{ isset($plans) ? count($plans) : 0 }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-4 text-center">
        <h5>Settings</h5>
        <a href="{{ route('admin.settings') }}" class="btn btn-info text-white mt-2">
          <i class="bi bi-pencil-square"></i> Edit Admin Info
        </a>
      </div>
    </div>
  </div>

  {{-- Quick Actions --}}
  <div class="card quick-actions mt-4">
    <h4><i class="bi bi-lightning-charge text-info"></i> Quick Actions</h4>
    <div class="mt-3 d-flex flex-wrap gap-3">
      <a href="{{ route('admin.products.create') }}" class="btn btn-outline-dark">
        <i class="bi bi-plus-circle"></i> Add Product
      </a>
      <a href="{{ route('admin.plans.create') }}" class="btn btn-outline-success">
        <i class="bi bi-diagram-3"></i> Add Plan
      </a>
      <a href="{{ route('admin.settings') }}" class="btn btn-outline-primary">
        <i class="bi bi-gear"></i> Settings
      </a>
    </div>
  </div>
</div>
@endsection
