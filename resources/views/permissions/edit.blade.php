@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Permission</h2>
    <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('permissions._form', ['permission' => $permission])
        <button type="submit" class="btn btn-primary">Update Permission</button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
