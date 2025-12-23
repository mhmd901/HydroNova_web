@extends('layouts.app')

@section('title', 'My Orders | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
            <div>
                <h1 class="h4 fw-bold mb-1">My Orders</h1>
                <p class="text-muted mb-0">Track your orders and their latest status.</p>
            </div>
            <a href="{{ route('main.products') }}" class="btn btn-teal"><i class="bi bi-plus-circle"></i> Continue shopping</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if (count($orders))
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $statusStyles = [
                                        'Pending'    => 'bg-warning text-dark',
                                        'Confirmed'  => 'bg-primary',
                                        'Processing' => 'bg-info text-dark',
                                        'Shipped'    => 'bg-secondary',
                                        'Delivered'  => 'bg-success',
                                        'Cancelled'  => 'bg-danger',
                                    ];
                                @endphp
                                @foreach ($orders as $order)
                                    @php
                                        $items = $order['items'] ?? [];
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold">{{ $order['id'] ?? $order['key'] }}</td>
                                        <td>{{ $order['created_at'] ?? '' }}</td>
                                        <td>
                                            <span class="badge {{ $statusStyles[$order['status'] ?? 'Pending'] ?? 'bg-secondary' }}">
                                                {{ $order['status'] ?? 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">${{ number_format((float)($order['total'] ?? 0), 2) }}</td>
                                        <td class="text-center">{{ is_array($items) ? count($items) : 0 }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('orders.show', $order['key']) }}" class="btn btn-outline-secondary btn-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-bag text-muted fs-1 mb-3"></i>
                        <h5 class="fw-semibold">No orders yet</h5>
                        <p class="text-muted mb-3">When you place an order, you can track it here.</p>
                        <a href="{{ route('main.products') }}" class="btn btn-teal">Browse products</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
