<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index () 
    {
        $roles = Role::latest()->get();
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
            'message' => 'Peran baru berhasil disimpan.',
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
            'message' => 'Peran berhasil diupdate.',
            'data' => $role
        ]);
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        if($role) {
            // delete
            $role->delete();
        }

        return redirect()->back()->with('success', "Data peran berhasil dihapus.");
        
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

        $roles = Role::whereIn('id', $request->role_id)->pluck('name')->toArray();
        $user = User::findOrFail($request->user_id); 
        
        // $rolesString = implode("','", $roles);
        // sprintf("\$user->syncRoles('%s');", $rolesString);
        $user->syncRoles($roles);
        return back()->with('success', 'Peran user berhasil disimpan.');
        
    }

    public function info() {
        return view('roles.info');
    }

}
