@extends('layouts.admin')

@section('content')
<h1>{{ $plan['name'] ?? '' }}</h1>
<p>Price: {{ $plan['price'] ?? '' }}</p>
<p>{{ $plan['description'] ?? '' }}</p>
<a href="{{ route('plans.index') }}">Back to Plans</a>
@endsection
