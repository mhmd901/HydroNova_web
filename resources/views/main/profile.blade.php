@extends('layouts.app')

@section('title', 'Profile | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Your profile</h1>
                        <p class="text-muted mb-0">Manage your delivery details for faster checkout.</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-bag-check"></i> My Orders
                    </a>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                            @csrf

                            <div class="col-12">
                                <label class="form-label fw-semibold">Full name</label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $profile['full_name'] ?? $customer['full_name'] ?? '') }}" required maxlength="120">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Email (read-only)</label>
                                <input type="email" class="form-control" value="{{ $customer['email'] ?? '' }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone number</label>
                                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $profile['phone'] ?? '') }}" required maxlength="30">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City (optional)</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $profile['city'] ?? '') }}" maxlength="120" placeholder="Beirut">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Address</label>
                                <textarea name="address" class="form-control" rows="3" required maxlength="255" placeholder="Street, building, floor">{{ old('address', $profile['address'] ?? '') }}</textarea>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-teal px-4">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
