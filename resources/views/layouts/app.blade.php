<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HydroNova - Smart Water Technology')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --teal: #2DAA9E;
            --teal-dark: #218d83;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f9fb;
            color: #212529;
            padding-top: 90px;
        }

        .navbar-brand img {
            height: 42px;
        }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .text-teal {
            color: var(--teal) !important;
        }

        .btn-teal {
            background: var(--teal);
            color: #fff;
            border: none;
        }

        .btn-teal:hover,
        .btn-teal:focus {
            background: var(--teal-dark);
            color: #fff;
        }

        .cart-badge {
            position: absolute;
            top: 2px;
            right: -8px;
            background: #dc3545;
            color: #fff;
            border-radius: 12px;
            padding: 0 6px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('images/hydronova_logo.png') }}" alt="HydroNova">
                <span class="fw-bold text-teal">HydroNova</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'text-teal fw-semibold' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('main.products') ? 'text-teal fw-semibold' : '' }}" href="{{ route('main.products') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('main.plans') ? 'text-teal fw-semibold' : '' }}" href="{{ route('main.plans') }}">Plans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-1 {{ request()->routeIs('assistant.index') ? 'text-teal fw-semibold' : '' }}" href="{{ route('assistant.index') }}">
                            <i class="bi bi-robot"></i>
                            <span>AI Assistant</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('main.contact') ? 'text-teal fw-semibold' : '' }}" href="{{ route('main.contact') }}">Contact</a>
                    </li>
                    @php
                        $cartItems = session('cart', []);
                        $navCartCount = array_sum(array_map(fn ($item) => $item['quantity'] ?? 0, $cartItems));
                        $customer = session('customer');
                    @endphp
                    <li class="nav-item ms-lg-3 position-relative">
                        <a class="nav-link position-relative" href="{{ $customer ? route('cart.index') : route('login') }}" aria-label="View cart">
                            <i class="bi bi-cart3 fs-5"></i>
                            <span class="cart-badge" data-cart-count>{{ $navCartCount }}</span>
                        </a>
                    </li>
                    @if (!$customer)
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-outline-secondary px-3" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-teal px-3" href="{{ route('register') }}">Register</a>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'text-teal fw-semibold' : '' }}" href="{{ route('profile.edit') }}">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'text-teal fw-semibold' : '' }}" href="{{ route('orders.index') }}">My Orders</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-link nav-link text-decoration-none" type="submit">Logout</button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container pt-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('auth_notice'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('auth_notice') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        @yield('content')
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"
    ></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    @yield('scripts')
</body>
</html>
