<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'route' => 'required|string|max:100|unique:menus,route',
            'permission' => 'required|string|max:100|unique:menus,permission',
        ]);

        if ($request->parent_id) {
            $request->validate([
                'parent_id' => 'exists:menus,id',
            ]);
        }
        if ($request->order) {
            $request->validate([
                'order' => 'integer',
            ]);
        }

        DB::beginTransaction();
        try {

            // cek parent
            $parentMenu = null;
            if ($request->parent_id) {
                $parentMenu = Menu::find($request->parent_id);
                if (!$parentMenu) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Menu Utama tidak ditemukan.',
                    ], 404);
                } else {
                    // order terakhir dari parent
                    $lastOrder = Menu::where('parent_id', $request->parent_id)->max('order');
                    if (!$request->order) {
                        $request->order = $lastOrder ? $lastOrder + 1 : 1;
                    } else {
                        // geser order menu lain jika ada yang sama
                        Menu::where('parent_id', $request->parent_id)
                            ->where('order', '>=', $request->order)
                            ->increment('order');
                    }
                }
            } else {
                // order terakhir dari menu utama
                $lastOrder = Menu::whereNull('parent_id')->max('order');
                if (!$request->order) {
                    $request->order = $lastOrder ? $lastOrder + 1 : 1;
                } else {
                    // geser order menu lain jika ada yang sama
                    Menu::whereNull('parent_id')
                        ->where('order', '>=', $request->order)
                        ->increment('order');
                }
            }
         
            $menu = Menu::create([
                'name' => $request->name,
                'route' => $request->route,
                'icon' => $request->icon,
                'order' => $request->order,
                'permission' => $request->permission,
                'parent_id' => $request->parent_id,
            ]);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Menu baru berhasil ditambahkan.',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan menu, Hubungi administrator.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id) 
    {
        $menu = Menu::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $menu
        ]);
    }

    public function update(Request $request, $id) 
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:menus,name,'.$id,
            'route' => 'required|string|max:100|unique:menus,route,'.$id,
            'permission' => 'required|string|max:100|unique:menus,permission,'.$id,
        ]);

        if ($request->parent_id) {
            $request->validate([
                'parent_id' => 'exists:menus,id',
            ]);
        }
        if ($request->order) {
            $request->validate([
                'order' => 'integer',
            ]);
        }

        DB::beginTransaction();
        try {
            // cek parent
            $parentMenu = null;
            if ($request->parent_id) {
                $parentMenu = Menu::find($request->parent_id);
                if (!$parentMenu) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Menu Utama tidak ditemukan.',
                    ], 404);
                } else {
                    // order terakhir dari parent
                    $lastOrder = Menu::where('parent_id', $request->parent_id)->max('order');
                    if (!$request->order) {
                        $request->order = $lastOrder ? $lastOrder + 1 : 1;
                    } else {
                        // geser order menu lain jika ada yang sama
                        Menu::where('parent_id', $request->parent_id)
                            ->where('order', '>=', $request->order)
                            ->increment('order');
                    }
                }
            } else {
                // order terakhir dari menu utama
                $lastOrder = Menu::whereNull('parent_id')->max('order');
                if (!$request->order) {
                    $request->order = $lastOrder ? $lastOrder + 1 : 1;
                } else {
                    // geser order menu lain jika ada yang sama
                    Menu::whereNull('parent_id')
                        ->where('order', '>=', $request->order)
                        ->increment('order');
                }
            }

            $menu = Menu::findOrFail($id);
            $menu->update([
                'name' => $request->name,
                'route' => $request->route,
                'icon' => $request->icon,
                'order' => $request->order,
                'permission' => $request->permission,
                'parent_id' => $request->parent_id
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Menu berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat update menu, Hubungi administrator.',
                'error' => $e->getMessage()
            ], 500);
        } 
    }

    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        if($menu) {
            // delete
            $menu->delete();
        }

        return redirect()->back()->with('success', "Menu berhasil dihapus.");
        
    }
}
