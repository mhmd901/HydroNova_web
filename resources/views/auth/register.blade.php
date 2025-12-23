@extends('layouts.app')

@section('title', 'Register | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 fw-bold mb-3 text-center">Create your HydroNova account</h1>
                        <p class="text-muted text-center mb-4">Save your details, manage orders, and checkout faster.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label">Full name</label>
                                <input id="full_name" class="form-control" type="text" name="full_name" value="{{ old('full_name') }}" required autofocus autocomplete="name">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Password</label>
                                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Confirm password</label>
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-teal" type="submit">Create account</button>
                            </div>

                            <div class="col-12 text-center">
                                <span class="text-muted">Already have an account?</span>
                                <a href="{{ route('login') }}" class="text-teal text-decoration-none fw-semibold">Log in</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
