@extends('layouts.app')

@section('title', 'Plans')

@section('content')
<h2>Our Plans</h2>

@if(!empty($plans))
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
    </tr>
    @foreach($plans as $id => $plan)
    <tr>
        <td>{{ $plan['name'] }}</td>
        <td>${{ $plan['price'] }}</td>
    </tr>
    @endforeach
</table>
@else
<p>No plans available.</p>
@endif
@endsection
