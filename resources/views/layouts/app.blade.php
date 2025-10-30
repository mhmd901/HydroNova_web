<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HydroNova – Smart Water Technology</title>

  {{-- 🌐 Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- 💎 Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  {{-- ✨ Custom CSS --}}
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      color: #212529;
      padding-bottom: 120px; /* Space for navbar */
      scroll-behavior: smooth;
    }

    /* 🌊 Refined Bottom Navbar */
    .bottom-nav {
      position: fixed;
      bottom: 30px; /* add margin bottom */
      left: 50%;
      transform: translateX(-50%);
      width: 70%; /* smaller width */
      max-width: 500px;
      background: #033f4c;
      border-radius: 40px;
      padding: 10px 0;
      z-index: 1000;
      display: flex;
      justify-content: space-around;
      align-items: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }

    .nav-item {
      text-decoration: none;
      color: #f1f1f1;
      text-align: center;
      flex: 1;
      transition: all 0.3s ease;
    }

    .nav-item i {
      font-size: 1.3rem;
      display: block;
      margin-bottom: 3px;
    }

    .nav-item span {
      font-size: 0.8rem;
      font-weight: 500;
    }

    .nav-item:hover {
      color: #0dcaf0;
      transform: translateY(-3px);
    }

    .nav-item.active {
      color: #00c9a7;
    }

    @media (max-width: 576px) {
      .bottom-nav {
        width: 90%;
        bottom: 15px;
        padding: 8px 0;
      }
      .nav-item i {
        font-size: 1.2rem;
      }
      .nav-item span {
        font-size: 0.7rem;
      }
    }

    /* 🌌 Smooth Fade Animation */
    body.fade-in {
      animation: fadeIn 0.7s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>

<body class="fade-in">

  <main class="pb-5">
    @yield('content')
  </main>

  {{-- 🌊 Compact Bottom Navbar --}}
  <nav class="bottom-nav shadow-lg">
    <a href="{{ route('main.index') }}" 
       class="nav-item {{ request()->routeIs('main.index') ? 'active' : '' }}">
       <i class="bi bi-house-door"></i>
       <span>Home</span>
    </a>

    <a href="{{ route('main.products') }}" 
       class="nav-item {{ request()->routeIs('main.products') ? 'active' : '' }}">
       <i class="bi bi-droplet"></i>
       <span>Products</span>
    </a>

    <a href="{{ route('main.plans') }}" 
       class="nav-item {{ request()->routeIs('main.plans') ? 'active' : '' }}">
       <i class="bi bi-box"></i>
       <span>Plans</span>
    </a>
  </nav>

  {{-- 🚀 Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
