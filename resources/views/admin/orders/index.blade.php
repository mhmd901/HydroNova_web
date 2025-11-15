@extends('layouts.admin')

@section('content')
@php
    $statusStyles = [
        'Pending'   => 'background:#f7c948;color:#3f3000;',
        'Confirmed' => 'background:#0d6efd;color:#fff;',
        'Shipped'   => 'background:#6f42c1;color:#fff;',
        'Completed' => 'background:#198754;color:#fff;',
        'Canceled'  => 'background:#dc3545;color:#fff;',
    ];
@endphp

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Orders</h2>
            <p class="text-muted mb-0">Monitor every WhatsApp order flowing through HydroNova.</p>
        </div>
        <span class="badge bg-secondary fs-6 px-3 py-2">{{ count($orders) }} total</span>
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if (count($orders))
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $status = $order['status'] ?? 'Pending';
                                    $createdAt = $order['created_at'] ?? '';
                                    $formattedDate = $createdAt ? \Carbon\Carbon::parse($createdAt)->format('M d, Y - H:i') : 'N/A';
                                    $phone = $order['phone'] ?? '';
                                    $phoneDisplay = $phone ? '+' . ltrim($phone, '+') : 'N/A';
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $order['id'] ?? 'N/A' }}</td>
                                    <td>{{ $order['name'] ?? 'N/A' }}</td>
                                    <td>
                                        @if ($phone)
                                            <div class="d-flex flex-column">
                                                <a href="tel:{{ $phone }}" class="text-decoration-none">{{ $phoneDisplay }}</a>
                                                <a href="https://wa.me/{{ ltrim($phone, '+') }}" target="_blank" class="small text-success text-decoration-none">
                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-truncate" style="max-width: 220px;">{{ $order['address'] ?? 'N/A' }}</td>
                                    <td class="fw-bold">${{ number_format((float)($order['total'] ?? 0), 2) }}</td>
                                    <td>{{ $formattedDate }}</td>
                                    <td>
                                        <span class="badge rounded-pill" style="{{ $statusStyles[$status] ?? 'background:#6c757d;color:#fff;' }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.orders.show', $order['key']) }}" class="btn btn-outline-secondary">View</a>
                                            <a href="{{ route('admin.orders.edit', $order['key']) }}" class="btn btn-outline-primary">Edit</a>
                                            <form action="{{ route('admin.orders.destroy', $order['key']) }}" method="POST" onsubmit="return confirm('Delete order {{ $order['id'] ?? '' }}?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
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
                    <p class="text-muted mb-0">New WhatsApp orders will appear here automatically.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
