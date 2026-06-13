@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Permission Details</h2>
    <a href="{{ route('permissions.index') }}" class="btn btn-secondary mb-3">Back to List</a>

    <table class="table table-bordered">
        <tr><th>ID</th><td>{{ $permission->id }}</td></tr>
        <tr><th>Name</th><td>{{ $permission->name }}</td></tr>
    </table>
</div>
@endsection
