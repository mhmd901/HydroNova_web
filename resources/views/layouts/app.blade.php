<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; }
        header { background:#007BFF; color:white; padding:10px; }
        header h1 { display:inline-block; margin:0; }
        nav { display:inline-block; margin-left:20px; }
        nav a { color:white; margin-right:15px; text-decoration:none; }
        footer { background:#f1f1f1; text-align:center; padding:10px; margin-top:20px; }
        main { padding:20px; }
        table { width:100%; border-collapse: collapse; margin-top:10px; }
        table, th, td { border:1px solid #ccc; }
        th, td { padding:8px; text-align:left; }
        .btn { padding:5px 10px; text-decoration:none; border-radius:3px; }
        .btn-add { background:#28a745; color:white; }
        .btn-edit { background:#ffc107; color:white; }
        .btn-delete { background:#dc3545; color:white; }
    </style>
</head>
<body>
    <header>
        <h1>Hydronova</h1>
        <nav>
            <a href="{{ route('main.index') }}">Home</a>
            <a href="{{ route('main.products') }}">Products</a>
            <a href="{{ route('main.plans') }}">Plans</a>
            @if(session()->has('admin_logged_in'))
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.logout') }}">Logout</a>
            @else
                <a href="{{ route('admin.login') }}">Admin Login</a>
            @endif
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Hydronova. All rights reserved.</p>
    </footer>
</body>
</html>
