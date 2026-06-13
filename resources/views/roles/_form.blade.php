<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Permissions</label><br>
    @foreach ($permissions as $permission)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                @if (in_array($permission->name, old('permissions', $rolePermissions ?? []))) checked @endif>
            <label class="form-check-label">{{ $permission->name }}</label>
        </div>
    @endforeach
</div>
