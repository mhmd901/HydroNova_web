@extends('layouts.app')

@section('title', 'Forgot Password | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 fw-bold mb-3 text-center">Reset your password</h1>
                        <p class="text-muted text-center mb-4">Enter the email linked to your account and we will send reset instructions.</p>

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
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

                        <form method="POST" action="{{ route('password.email') }}" class="row g-3">
                            @csrf

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-teal" type="submit">Send reset link</button>
                            </div>

                            <div class="col-12 text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none text-teal">Back to login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
