<div class="row">
	<div class="col-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Name</label>
		<div class="col-md-6">
			<input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
		</div>
	</div>

	<div class="col-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Email</label>
		<div class="col-md-6">
			<input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
		</div>
	</div>
</div>

<div class="row">

	<div class="col-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Password @if($user) <small>(Leave blank to keep current password)</small> @endif</label>
		<div class="col-md-6">
			<input type="password" name="password" class="form-control" @if(!$user) required @endif>
		</div>
	</div>

	<div class="col-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Confirm Password</label>
		<div class="col-md-6">
			<input type="password" name="password_confirmation" class="form-control" @if(!$user) required @endif>
		</div>
	</div>
</div>

<div class="row">
	<div class="ol-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Plant</label>
		<div class="col-md-6">
			<select style="width:40%;" name="plant_code[]" class="form-control" multiple required>
				@foreach ($plants as $plant)
					<option value="{{ $plant->plant_code }}"
						@if (isset($plantcode) && in_array($plant->plant_code, $plantcode))
							selected
						@endif
					>
						{{ $plant->plant_name }}
					</option>
				@endforeach
			</select>
		
		</div>
	</div>
	
	<div class="ol-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Location</label>
		<div class="col-md-6">
			<select style="width:40%;" name="loc_code[]" class="form-control" multiple required>
				@foreach ($locations as $location)
					<option value="{{ $location->loc_code }}"
						
						@if (isset($loccode) && in_array($location->loc_code, $loccode))
							selected
						@endif
					>
						{{ $location->loc_name }}
					</option>
				@endforeach
			</select>
		
		</div>
	</div>

</div>

<div class="row">
	<div class="ol-md-6 mb-3 dispflex">
		<label class="col-md-2 lblcommon">Roles</label>
		<div class="col-md-6">
			<select style="width:100%;" name="roles[]" class="form-control" multiple required>
				@foreach ($roles as $role)
					<option value="{{ $role->name }}"
						@if (isset($userRoles) && in_array($role->name, $userRoles))
							selected
						@endif
					>
						{{ $role->name }}
					</option>
				@endforeach
			</select>
		
			<small class="form-text text-muted">Hold Ctrl (Cmd on Mac) to select multiple roles.</small>
		</div>
	</div>

</div>
