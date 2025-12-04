<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HydroNova Admin Panel</title>

  {{-- Bootstrap & Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  {{-- Global Light Theme --}}
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
  <div class="d-flex">
    {{-- Sidebar --}}
    <div class="sidebar p-3">
      <div class="text-center mb-4">
        <img src="{{ asset('images/hydronova_logo.png') }}" alt="HydroNova Logo" width="90">
        <h5 class="mt-2 text-success">HydroNova</h5>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> Products
      </a>
      <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i> Plans
      </a>
      <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i class="bi bi-bag-check"></i> Orders
      </a>
      <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <i class="bi bi-gear"></i> Settings
      </a>

      <hr>
      <a href="{{ route('admin.logout') }}" class="nav-link text-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>

    {{-- Main content --}}
    <div class="flex-grow-1">
      <div class="header d-flex justify-content-between align-items-center">
        <h1 class="m-0">HydroNova Admin Panel</h1>
        <div class="text-muted">
          <i class="bi bi-person-circle"></i> {{ session('admin_username', 'admin') }}
        </div>
      </div>

      <main class="p-4">
        @yield('content')
      </main>

      <footer class="footer">
        <small>© 2025 HydroNova | Empowering Water with Technology 💧</small>
      </footer>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>
</html>
