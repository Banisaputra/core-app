<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index () 
    {
        $menus = Menu::latest()->get();
        return view('menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:menus,name',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'route' => $request->route,
            'icon' => $request->icon,
            'order' => $request->order,
            'permission' => $request->permisson,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu baru berhasil ditambahkan.',
            'data' => $menu
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
            'name' => 'required|string|max:100|unique:menus,name,'.$id,
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
}
