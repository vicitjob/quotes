@extends('layouts.app')

@section('content')
<div class="container">
    
	<div class="row headerrow">
		<div class="col-md-12">
			<h6 class="frmhead">Edit - Gate Ref No. : {{ old('gate_in_no', $gateentry->gate_in_no ?? '') }}<span class="operatorname">Created By: {{ old('createdby_name', $op_name) }}</span></h6>
		</div>
		
	</div>
	 @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
    <form action="{{ route('gateentries.update', $gateentry->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return validatentryform();">
        @csrf
        @method('PUT')
        @include('gateentries._form', ['gateentry' => $gateentry])
		
		<?php if($is_storeuser && ($gateentry->status == 1)) { ?>
		<a href="javascript:void(0);" onclick="save_storeuser_data();" id="savestoredata" class="btn btn-primary">Save & Confirm</a>
		<?php } else { ?>
		<button type="submit" id="savebtn" class="btn btn-primary" onclick="validateEntries();">Save & Confirm</button>
		<?php } ?>
		
        <a href="{{ route('gateentries.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
let materialIndex = {{ $materialDetails->count() }};
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
