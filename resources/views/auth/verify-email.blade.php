@extends('layouts.app')

@section('title', 'Verify Email | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h5 fw-bold mb-3 text-center">Verify your email</h1>
                        <p class="text-muted text-center mb-4">
                            Before continuing, please check your email for a verification link.
                            If you didn't receive the email, we will gladly send another.
                        </p>

                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success">
                                A new verification link has been sent to the email address you provided during registration.
                            </div>
                        @endif

                        <div class="d-flex flex-column gap-2">
                            <form method="POST" action="{{ route('verification.send') }}" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-teal">Resend verification email</button>
                            </form>

                            <form method="POST" action="{{ route('logout') }}" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
