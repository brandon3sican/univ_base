<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserManagementController extends Controller
{
    protected $middleware = ['auth'];

    public function index()
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers()) {
            abort(403, 'You do not have permission to manage users.');
        }

        $users = User::with('roles')->get();
        return view('user-management.index', compact('users'));
    }

    public function create()
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers()) {
            abort(403, 'You do not have permission to create users.');
        }

        $roles = Role::all();
        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers()) {
            abort(403, 'You do not have permission to create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:ub_users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:ub_roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->roles()->attach($validated['role_id']);

        return redirect()->route('user-management.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers()) {
            abort(403, 'You do not have permission to edit users.');
        }

        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers()) {
            abort(403, 'You do not have permission to edit users.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:ub_users,email,' . $id,
            'role_id' => 'required|exists:ub_roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->roles()->sync([$validated['role_id']]);

        return redirect()->route('user-management.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers()) {
            abort(403, 'You do not have permission to delete users.');
        }

        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('user-management.index')->with('success', 'User deleted successfully.');
    }
}
