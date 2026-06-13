@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Permission</h2>
    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        @include('permissions._form', ['permission' => null])
        <button type="submit" class="btn btn-primary">Save Permission</button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
