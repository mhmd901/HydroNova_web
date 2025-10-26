@extends('layouts.admin')

@section('content')
<h1>Edit Product</h1>
<form action="{{ route('products.update', $id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Name:</label><br>
    <input type="text" name="name" value="{{ $product['name'] ?? '' }}" required><br><br>
    <label>Price:</label><br>
    <input type="number" name="price" value="{{ $product['price'] ?? '' }}" required><br><br>
    <label>Description:</label><br>
    <textarea name="description">{{ $product['description'] ?? '' }}</textarea><br><br>
    <button type="submit">Update</button>
</form>
@endsection
