@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row headerrow">
		<div class="col-md-8">
			<h4>Edit User</h4>
		</div>
		
	</div>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('users._form', ['user' => $user])
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
