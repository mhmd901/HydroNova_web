@extends('layouts.app')

@section('title', 'Login | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 fw-bold mb-3 text-center">Welcome back</h1>
                        <p class="text-muted text-center mb-4">Log in to manage your cart, profile, and orders.</p>

                        @if (session('auth_notice'))
                            <div class="alert alert-info">{{ session('auth_notice') }}</div>
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

                        <form method="POST" action="{{ route('login') }}" class="row g-3">
                            @csrf
                            <input type="hidden" name="intended" value="{{ request('intended') }}">
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label">Password</label>
                                </div>
                                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-teal" type="submit">Log in</button>
                            </div>

                            <div class="col-12 text-center">
                                <span class="text-muted">New here?</span>
                                <a href="{{ route('register') }}" class="text-teal text-decoration-none fw-semibold">Create an account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
