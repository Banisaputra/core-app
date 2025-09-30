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

    public function asign($id) 
    {
        $role = Role::findById($id);
        $allPermissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('permissions.asign', compact('role','allPermissions','rolePermissions'));
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
            'permission' => 'required|string',
            'type' => 'required|string',
            'is_row' => 'required',
        ]);
        if ($request->is_row == 1) {
            // cek permission
            $permission = Permission::where('name', $request->permission)->first();
            if (!$permission) return response()->json([
                    'success' => false,
                    'message' => 'Izin akses tidak valid.'
                ], 404);
    
            $role = Role::findOrFail($request->role_id);
            if (!$role)  return response()->json([
                    'success' => false,
                    'message' => 'Peran untuk izin akses tidak valid.'
                ], 404);
            if (strtoupper($request->type) === "GIVEN") {
                $role->givePermissionTo($request->permission);
            } else if (strtoupper($request->type) === "REVOKE") {
                $role->revokePermissionTo($request->permission);
            }
        } else {
            $permissions = [];
            if (strtoupper($request->type) == "GIVEN") {
                $permissions = Permission::pluck('name')->toArray();
            } 
            $role = Role::findOrFail($request->role_id);
            $role->syncPermissions($permissions);
        }




        return response()->json([
            'success' => true,
            'message' => 'Izin akses peran berhasil disimpan.'
        ]);
        
    }
}
