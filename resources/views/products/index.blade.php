@extends('layouts.app')

@section('content')
<h1>Our Products</h1>
<ul>
    @foreach($products as $id => $product)
        <li>
            <a href="{{ route('products.show', $id) }}">{{ $product['name'] ?? '' }}</a>
            - Price: {{ $product['price'] ?? '' }}
        </li>
    @endforeach
</ul>
@endsection
