@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <h2 class="fw-bold mb-4"><i class="bi bi-box-seam text-info"></i> Products</h2>

  <a href="{{ route('admin.products.create') }}" class="btn btn-outline-success mb-3">
    <i class="bi bi-plus-circle"></i> Add Product
  </a>

  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>Name</th>
        <th>Price ($)</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $id => $product)
        <tr>
          <td>{{ $product['name'] ?? 'Unnamed' }}</td>
          <td>${{ $product['price'] ?? '0.00' }}</td>
          <td class="text-end">
            <a href="{{ route('admin.products.edit', $id) }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('admin.products.destroy', $id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="3" class="text-center text-muted">No products available.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
