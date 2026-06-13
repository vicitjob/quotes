@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row headerrow">
		<div class="col-md-8">
			<h4>Create User</h4>
		</div>
		
	</div>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        @include('users._form', ['user' => null])
        <button type="submit" class="btn btn-primary">Save User</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
