@extends('layouts.app')

@section('title', 'Add Plan')

@section('content')
<h2>Add Plan</h2>

<form action="{{ route('admin.plans.store') }}" method="POST">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" required>
    <br><br>
    <label>Price:</label>
    <input type="number" name="price" required>
    <br><br>
    <button type="submit">Save</button>
</form>
@endsection
