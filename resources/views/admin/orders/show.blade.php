@extends('layouts.admin')

@section('content')
@php
    $statusPalette = [
        'Pending'   => ['label' => 'Pending Review', 'color' => '#f7c948', 'text' => '#3f3000'],
        'Confirmed' => ['label' => 'Confirmed', 'color' => '#0d6efd', 'text' => '#fff'],
        'Shipped'   => ['label' => 'Shipped', 'color' => '#6f42c1', 'text' => '#fff'],
        'Completed' => ['label' => 'Completed', 'color' => '#198754', 'text' => '#fff'],
        'Canceled'  => ['label' => 'Canceled', 'color' => '#dc3545', 'text' => '#fff'],
    ];
    $currentStatus = $order['status'] ?? 'Pending';
    $currentStatusIndex = array_search($currentStatus, $statusOptions, true);
    $activeStatusColors = $statusPalette[$currentStatus] ?? ['color' => '#6c757d', 'text' => '#fff'];
@endphp

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Order {{ $order['id'] ?? '' }}</h2>
            <p class="text-muted mb-0">Placed {{ $order['created_at'] ?? 'N/A' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.orders.edit', $order['key']) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Status
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h4 class="fw-semibold mb-3">Items</h4>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order['items'] ?? [] as $item)
                                    <tr>
                                        <td>{{ $item['name'] ?? 'Product' }}</td>
                                        <td class="text-center">{{ $item['quantity'] ?? $item['qty'] ?? 1 }}</td>
                                        <td class="text-center">${{ number_format((float)($item['price'] ?? 0), 2) }}</td>
                                        <td class="text-end">${{ number_format((float)($item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1))), 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Grand Total</span>
                        <span class="fs-4 fw-bold">${{ number_format((float)($order['total'] ?? 0), 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="fw-semibold mb-3">Status Timeline</h4>
                    <ul class="list-unstyled mb-0">
                        @foreach ($statusOptions as $index => $status)
                            @php
                                $state = $statusPalette[$status] ?? ['color' => '#6c757d', 'text' => '#fff'];
                                $reached = $currentStatusIndex === false ? false : $index <= $currentStatusIndex;
                            @endphp
                            <li class="d-flex gap-3 mb-4 align-items-center">
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                      style="width:40px;height:40px;background:{{ $reached ? $state['color'] : '#e9ecef' }};color:{{ $reached ? $state['text'] : '#6c757d' }};">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <p class="mb-0 fw-semibold">{{ $status }}</p>
                                    <small class="text-muted">
                                        {{ $reached ? 'Completed' : 'Awaiting update' }}
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h4 class="fw-semibold mb-3">Customer</h4>
                    <p class="mb-1 fw-semibold">{{ $order['name'] ?? 'N/A' }}</p>
                    <p class="mb-1">
                        @if (!empty($order['phone']))
                            <a href="tel:{{ $order['phone'] }}" class="text-decoration-none">
                                <i class="bi bi-telephone text-success me-1"></i>{{ $order['phone'] }}
                            </a>
                        @else
                            <span class="text-muted">No phone</span>
                        @endif
                    </p>
                    <p class="mb-3 text-muted">{{ $order['address'] ?? 'N/A' }}</p>
                    @if (!empty($order['note']))
                        <div class="p-3 rounded bg-light">
                            <p class="fw-semibold mb-1">Note</p>
                            <p class="mb-0 text-muted">{{ $order['note'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="fw-semibold mb-3">Status</h4>
                    <span class="badge rounded-pill px-3 py-2"
                          style="background:{{ $activeStatusColors['color'] }};color:{{ $activeStatusColors['text'] }};">
                        {{ $currentStatus }}
                    </span>
                    <div class="mt-3">
                        @if (!empty($order['phone']))
                            <a href="https://wa.me/{{ ltrim($order['phone'], '+') }}" target="_blank" class="btn btn-outline-success w-100">
                                <i class="bi bi-whatsapp"></i> Message Customer
                            </a>
                        @else
                            <button class="btn btn-outline-secondary w-100" disabled>No phone provided</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
