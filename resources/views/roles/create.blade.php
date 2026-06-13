@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Role</h2>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        @include('roles._form', ['role' => null, 'rolePermissions' => [], 'permissions' => $permissions])
        <button type="submit" class="btn btn-primary">Save Role</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
