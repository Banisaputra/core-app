<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $this->authorize('user_edit');
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('user_edit');
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'roles' => 'array'
        ]);

        $user->update($request->only('name', 'email'));
        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully');
    }
}