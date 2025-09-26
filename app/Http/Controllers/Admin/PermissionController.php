<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index () 
    {
        $permissions = Permission::latest()->get();
        return view('permissions.index', compact('permissions'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Izin akses baru berhasil disimpan.',
            'data' => $permission
        ]);
    }

    public function edit($id) 
    {
        $permission = Permission::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $permission
        ]);
    }

    public function update(Request $request, $id) 
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name,'.$id,
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Izin akses berhasil diupdate.',
            'data' => $permission
        ]);
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        if($permission) {
            // delete
            $permission->delete();
        }

        return redirect()->back()->with('success', "Izin akses berhasil dihapus.");
        
    }

    public function asign() 
    {
        $permissions = Permission::all();
        return view('permissions.asign', compact('permissions'));
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $permissions = Permission::where('name', 'like', "%$search%")
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($permissions);
    }

    public function updatePermission(Request $request) 
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|array',
            'permission_id.*' => 'exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permission_id)->pluck('name')->toArray();
        $role = Role::findOrFail($request->role_id);
        
        // $permissionString = implode("','", $permissions);
        // sprintf("\$role->syncPermission('%s');", $permissionString);
        $role->syncPermissions($permissions);
        return back()->with('success', 'Izin akses peran berhasil disimpan.');
        
    }
}
