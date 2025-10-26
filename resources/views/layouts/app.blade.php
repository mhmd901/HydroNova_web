<!DOCTYPE html>
<html>
<head>
    <title>My Website</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav a { margin-right: 10px; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <nav>
        <a href="/">Home</a> |
        <a href="{{ route('plans.index') }}">Plans</a> |
        <a href="{{ route('products.index') }}">Products</a>
    </nav>
    <hr>
    @yield('content')
</body>
</html>
