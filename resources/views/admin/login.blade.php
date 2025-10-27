@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<h2>Admin Login</h2>

@if(session('error'))
    <div style="color:red;">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('admin.checkLogin') }}">
    @csrf
    <label>Username:</label>
    <input type="text" name="username" required>
    <br><br>
    <label>Password:</label>
    <input type="password" name="password" required>
    <br><br>
    <button type="submit">Login</button>
</form>
@endsection
