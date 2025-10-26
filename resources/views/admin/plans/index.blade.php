@extends('layouts.admin')

@section('content')
<h1>All Plans</h1>
<a href="{{ route('plans.create') }}">Add New Plan</a>
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
        @foreach($plans as $id => $plan)
        <tr>
            <td>{{ $id }}</td>
            <td>{{ $plan['name'] ?? '' }}</td>
            <td>{{ $plan['price'] ?? '' }}</td>
            <td>
                <a href="{{ route('plans.edit', $id) }}">Edit</a> |
                <form action="{{ route('plans.destroy', $id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form> |
                <a href="{{ route('plans.show', $id) }}">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
