@extends('layouts.admin')

@section('content')
<h1>Admin Dashboard</h1>
<p>Welcome to the Admin Panel. Use the links below to manage your site.</p>

<ul>
    <li><a href="{{ route('plans.index') }}">Manage Plans</a></li>
    <li><a href="{{ route('products.index') }}">Manage Products</a></li>
</ul>
@endsection
