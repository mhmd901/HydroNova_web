@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<h2>Edit Product</h2>

<form action="{{ route('admin.products.update', $id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Name:</label>
    <input type="text" name="name" value="{{ $product['name'] ?? '' }}" required>
    <br><br>
    <label>Price:</label>
    <input type="number" name="price" value="{{ $product['price'] ?? 0 }}" required>
    <br><br>
    <button type="submit">Update</button>
</form>
@endsection
