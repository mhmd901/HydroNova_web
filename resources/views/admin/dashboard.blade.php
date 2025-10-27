@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<h2>Admin Dashboard</h2>
<p>Manage Products and Plans</p>

<div style="margin-bottom: 20px;">
    <a class="btn btn-add" href="{{ route('admin.products.create') }}">Add Product</a>
    <a class="btn btn-add" href="{{ route('admin.plans.create') }}">Add Plan</a>
</div>

<h3>Products</h3>
@if(!empty($products))
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    @foreach($products as $id => $product)
    <tr>
        <td>{{ $product['name'] ?? '' }}</td>
        <td>${{ $product['price'] ?? 0 }}</td>
        <td>
            <a class="btn btn-edit" href="{{ route('admin.products.edit', $id) }}">Edit</a>
            <form action="{{ route('admin.products.destroy', $id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-delete" type="submit">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@else
<p>No products yet.</p>
@endif

<h3>Plans</h3>
@if(!empty($plans))
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>
    @foreach($plans as $id => $plan)
    <tr>
        <td>{{ $plan['name'] ?? '' }}</td>
        <td>${{ $plan['price'] ?? 0 }}</td>
        <td>
            <a class="btn btn-edit" href="{{ route('admin.plans.edit', $id) }}">Edit</a>
            <form action="{{ route('admin.plans.destroy', $id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-delete" type="submit">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@else
<p>No plans yet.</p>
@endif

@endsection
