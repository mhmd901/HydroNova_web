@extends('layouts.admin')

@section('content')
<h1>Edit Plan</h1>
<form action="{{ route('plans.update', $id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Name:</label><br>
    <input type="text" name="name" value="{{ $plan['name'] ?? '' }}" required><br><br>
    <label>Price:</label><br>
    <input type="number" name="price" value="{{ $plan['price'] ?? '' }}" required><br><br>
    <label>Description:</label><br>
    <textarea name="description">{{ $plan['description'] ?? '' }}</textarea><br><br>
    <button type="submit">Update</button>
</form>
@endsection
