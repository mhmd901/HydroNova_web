@extends('layouts.app')

@section('title', 'Confirm Password | HydroNova')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h5 fw-bold mb-3 text-center">Confirm your password</h1>
                        <p class="text-muted text-center mb-4">For security, please confirm your password before continuing.</p>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.confirm') }}" class="row g-3">
                            @csrf

                            <div class="col-12">
                                <label class="form-label">Password</label>
                                <input class="form-control" type="password" name="password" required autocomplete="current-password">
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-teal" type="submit">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
