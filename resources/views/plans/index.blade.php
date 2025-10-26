@extends('layouts.app')

@section('content')
<h1>Our Plans</h1>
<ul>
    @foreach($plans as $id => $plan)
        <li>
            <a href="{{ route('plans.show', $id) }}">{{ $plan['name'] ?? '' }}</a>
            - Price: {{ $plan['price'] ?? '' }}
        </li>
    @endforeach
</ul>
@endsection
