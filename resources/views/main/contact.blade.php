@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <h2>Contact Us</h2>
    <form action="#" method="POST">
        @csrf
        <label>Name:</label>
        <input type="text" name="name">
        <label>Email:</label>
        <input type="email" name="email">
        <label>Message:</label>
        <textarea name="message"></textarea>
        <button type="submit">Send</button>
    </form>
@endsection
