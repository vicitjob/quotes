@extends('layouts.app')

@section('content')
<div class="container">
   
	<div class="row headerrow">
		<div class="col-md-12">
			<h6 class="frmhead">Add Gate Entry <span class="operatorname">Initiating By: {{ old('createdby_name', $op_name) }}</span></h6>
		</div>
		
	</div>
	
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	
    <form action="{{ route('gateentries.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validatentryform();">
        @csrf
        @include('gateentries._form')
        <button type="submit" class="btn btn-primary" id="savebtn">Save</button>
        <a href="{{ route('gateentries.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>

window.onload = function() {
  
};

let materialIndex = 0;
function addMaterial() {
    const template = document.querySelector('.material-template').cloneNode(true);
    template.style.display = 'block';
    template.classList.remove('material-template');

    template.querySelectorAll('[data-name]').forEach(input => {
        const realName = input.getAttribute('data-name').replace('INDEX', materialIndex);
        input.setAttribute('name', realName);
        input.removeAttribute('data-name');

        // Add required attribute only on visible inputs
        input.setAttribute('required', 'required');

        input.value = '';
    });

    document.getElementById('materials').appendChild(template);
    materialIndex++;
}

function removeMaterial(button) {
    button.closest('.material-item').remove();
}

function validateEntries()
{
	console.log('Saving');
}


</script>

@endsection
