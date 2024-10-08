@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-black-50">You are logged in!</h1>
        <h2>Welcome, {{$user_name}}!</h2> <!-- Display the username -->
    </div>
@endsection
