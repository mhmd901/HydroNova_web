@extends('layouts.app')

@section('title', 'Reset Password | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 fw-bold mb-3 text-center">Choose a new password</h1>
                        <p class="text-muted text-center mb-4">Set a secure password to protect your account.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.store') }}" class="row g-3">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Password</label>
                                <input class="form-control" type="password" name="password" required autocomplete="new-password">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Confirm password</label>
                                <input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-teal" type="submit">Reset password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
