@extends('layouts.admin')

@section('content')
<h1>All Products</h1>
<a href="{{ route('products.create') }}">Add New Product</a>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $id => $product)
        <tr>
            <td>{{ $id }}</td>
            <td>{{ $product['name'] ?? '' }}</td>
            <td>{{ $product['price'] ?? '' }}</td>
            <td>
                <a href="{{ route('products.edit', $id) }}">Edit</a> |
                <form action="{{ route('products.destroy', $id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form> |
                <a href="{{ route('products.show', $id) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
