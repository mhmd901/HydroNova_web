@extends('layouts.app')

@section('title', 'Products')

@section('content')
<h2>Our Products</h2>

@if(!empty($products))
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
    </tr>
    @foreach($products as $id => $product)
    <tr>
        <td>{{ $product['name'] }}</td>
        <td>${{ $product['price'] }}</td>
    </tr>
    @endforeach
</table>
@else
<p>No products available.</p>
@endif
@endsection
