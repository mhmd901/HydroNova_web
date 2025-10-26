@extends('layouts.admin')

@section('content')
<h1>Add New Product</h1>
<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>
    <label>Price:</label><br>
    <input type="number" name="price" required><br><br>
    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>
    <button type="submit">Save</button>
</form>
@endsection
