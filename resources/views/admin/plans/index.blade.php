@extends('layouts.app')

@section('title', 'Manage Plans')

@section('content')
    <h2>Plans</h2>
    <a href="{{ route('admin.plans.create') }}">Add New Plan</a>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
            <tr>
                <td>{{ $plan->id }}</td>
                <td>{{ $plan->name }}</td>
                <td>{{ $plan->price }}</td>
                <td>
                    <a href="{{ route('admin.plans.edit', $plan->id) }}">Edit</a>
                    <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
