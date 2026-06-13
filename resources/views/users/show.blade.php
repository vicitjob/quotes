@extends('layouts.app')

@section('content')
<div class="container">
    <h2>User Details</h2>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mb-3">Back to List</a>

    <table class="table table-bordered">
        <tr><th>ID</th><td>{{ $user->id }}</td></tr>
        <tr><th>Name</th><td>{{ $user->name }}</td></tr>
        <tr><th>Email</th><td>{{ $user->email }}</td></tr>
        <tr><th>Created At</th><td>{{ $user->created_at }}</td></tr>
        <tr><th>Updated At</th><td>{{ $user->updated_at }}</td></tr>
    </table>
</div>
@endsection
