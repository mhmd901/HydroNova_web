<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav a { margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 10px; text-align: left; }
        form { display: inline; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('plans.index') }}">Plans</a> |
        <a href="{{ route('products.index') }}">Products</a>
    </nav>
    <hr>
    @yield('content')
</body>
</html>
