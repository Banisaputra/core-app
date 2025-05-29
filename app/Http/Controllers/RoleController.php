<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index () 
    {
        $roles = Role::latest()->paginate();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'data' => $role
        ]);
    }

    public function edit($id) 
    {
        $role = Role::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $role
        ]);
    }

    public function update(Request $request, $id) 
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,'.$id,
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    public function destroy(string $id)
    {
        $role = role::findOrFail($id);
        if($role) {
            // delete
            $role->delete();
        }

        return redirect()->back()->with('success', "Data dan akun anggota berhasil dihapus");
        
    }

    public function asign() 
    {
        $roles = Role::all();
        return view('roles.asign', compact('roles'));
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $roles = Role::where('name', 'like', "%$search%")
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($roles);
    }

    public function updateRoles(Request $request) 
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|array',
            'role_id.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Sync roles (removes existing and replaces with new)
        $user->roles()->sync($request->role_id);

        return back()->with('success', 'Roles updated successfully!');
        
    }

}
