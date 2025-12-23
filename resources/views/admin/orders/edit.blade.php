@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Update Order {{ $order->id ?? '' }}</h2>
            <p class="text-muted mb-0">Adjust the fulfillment status and review the latest cart snapshot.</p>
        </div>
        <a href="{{ route('admin.orders.show', $order->_key) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Order
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="fw-semibold mb-3">Status</h4>
                    <form action="{{ route('admin.orders.update', $order->_key) }}" method="POST" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-12">
                            <label class="form-label fw-semibold">Select Status</label>
                            <select name="status" class="form-select">
                                @foreach ($statusOptions as $status)
                                    <option value="{{ $status }}" @selected(($order->status ?? 'Pending') === $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="fw-semibold mb-3">Order Snapshot</h4>
                    <p class="mb-1"><strong>Customer:</strong> {{ $order->full_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $order->phone ?? 'N/A' }}</p>
                    <p class="mb-3"><strong>Address:</strong> {{ $order->address ?? 'N/A' }} @if($order->city) ({{ $order->city }}) @endif</p>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items ?? [] as $item)
                                    <tr>
                                        <td>{{ $item['name'] ?? $item['product_name'] ?? 'Product' }}</td>
                                        <td class="text-center">{{ $item['quantity'] ?? $item['qty'] ?? 1 }}</td>
                                        <td class="text-end">${{ number_format((float)($item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1))), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">Total</span>
                        <span class="fs-5 fw-bold">${{ number_format((float)($order->total ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
