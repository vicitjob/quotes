@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Role Details</h2>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-3">Back to List</a>

    <table class="table table-bordered">
        <tr><th>ID</th><td>{{ $role->id }}</td></tr>
        <tr><th>Name</th><td>{{ $role->name }}</td></tr>
        <tr>
            <th>Permissions</th>
            <td>
                @foreach ($role->permissions as $permission)
                    <span class="badge bg-secondary">{{ $permission->name }}</span>
                @endforeach
            </td>
        </tr>
    </table>
</div>
@endsection
