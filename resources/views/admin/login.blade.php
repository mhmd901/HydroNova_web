<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HydroNova Admin Login</title>

  {{-- 🌐 Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- 💎 Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      background: url('{{ asset("images/hero_bg.jpg") }}') center/cover no-repeat fixed;
      position: relative;
    }

    /* Add a dark overlay for better contrast */
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 30, 40, 0.65);
      backdrop-filter: blur(3px);
      z-index: 0;
    }

    .login-card {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.12);
      border-radius: 20px;
      backdrop-filter: blur(15px);
      padding: 40px 35px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-card h2 {
      font-weight: 700;
      margin-bottom: 20px;
      color: #ffffff;
    }

    .form-control {
      border-radius: 10px;
      background-color: rgba(255, 255, 255, 0.9);
      border: none;
      color: #333;
      transition: 0.3s;
    }

    .form-control:focus {
      box-shadow: 0 0 5px #00c9a7;
    }

    .btn-login {
      background-color: #00c9a7;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      padding: 10px;
      transition: all 0.3s;
      box-shadow: 0 4px 10px rgba(0, 201, 167, 0.3);
    }

    .btn-login:hover {
      background-color: #00b096;
      transform: scale(1.05);
      box-shadow: 0 4px 15px rgba(0, 201, 167, 0.5);
    }

    .alert-danger {
      background-color: rgba(255, 0, 0, 0.3);
      color: #fff;
      border: none;
      font-weight: 500;
    }

    .brand img {
      width: 70px;
      height: 70px;
      object-fit: contain;
      margin-bottom: 10px;
      filter: drop-shadow(0 0 5px rgba(0, 255, 255, 0.5));
    }

    .brand h3 {
      font-weight: 600;
      color: #00c9a7;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="login-card">
    {{-- 🌊 Logo + Brand --}}
    <div class="brand text-center">
      <img src="{{ asset('images/hydronova_logo.png') }}" alt="HydroNova Logo">
      <h3>HydroNova Admin</h3>
    </div>

    <h2>Welcome Back</h2>
    <p class="text-white-50 mb-4">Login to manage your dashboard</p>

    {{-- 🔴 Error Alert --}}
    @if (session('error'))
      <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif

    {{-- 🔐 Login Form --}}
    <form action="{{ route('admin.checkLogin') }}" method="POST">
      @csrf
      <div class="mb-3 text-start">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" required autofocus>
      </div>

      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-login w-100">
        <i class="bi bi-box-arrow-in-right me-2"></i> Login
      </button>
    </form>
  </div>

  {{-- 🚀 Bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
