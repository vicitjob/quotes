<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
		$roles = Role::all();
		$plants = DB::table('plant_master')->where('is_delete',0)->orderby('plant_name')->get();
		$locations = DB::table('location_master')->where('is_delete',0)->orderby('loc_name')->get();
		return view('users.create', compact('roles','plants','locations'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:users,email',
           'password' => 'required|min:6|confirmed',
		   'roles' => 'required|array',
		   'plant_code' => 'required|array',
		   'loc_code' => 'required|array'
        ]);
        
        $user = User::create([
           'name' => $request->name,
           'email' => $request->email,
           'password' => Hash::make($request->password),
		   'plant_code' => implode(',',$request->plant_code),
		   'loc_code' => implode(',',$request->loc_code)
        ]);
		
		$user->syncRoles($request->roles);
        
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
    
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
    
    public function edit(User $user)
    {
        $roles = Role::all();
		$userRoles = $user->roles->pluck('name')->toArray();
		$plants = DB::table('plant_master')->where('is_delete',0)->orderby('plant_name')->get();
		$plantcode = explode(',',$user->plant_code);
		$locations = DB::table('location_master')->where('is_delete',0)->orderby('loc_name')->get();
		$loccode = explode(',',$user->loc_code);
		
		return view('users.edit', compact('user', 'roles', 'userRoles','plants','locations','plantcode','loccode'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:users,email,' . $user->id,
		   'roles' => 'required|array',
		   'plant_code' => 'required|array',
		   'loc_code' => 'required|array'
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
		$user->plant_code = implode(',',$request->plant_code);
		$user->loc_code = implode(',',$request->loc_code);
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6|confirmed'
            ]);
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
		$user->syncRoles($request->roles);
        
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }
    
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}

