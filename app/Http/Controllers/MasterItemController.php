<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MasterItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MasterItem::all();
        return view('master_items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master_items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|alpha_num|unique:master_items,item_code',
            'item_name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'sales_price' => 'required|integer|min:0',
            'item_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // image path
        $photoPath = null;
        if ($request->hasFile('item_image')) {
            $photoPath = $request->file('item_image')->store('item_image', 'public');
        }
        
        MasterItem::create([
            "item_code" => $request->item_code,
            "item_name" => $request->item_name,
            "stock" => $request->stock,
            "sales_price" => $request->sales_price,
            "item_image" => $photoPath,
            "created_by" => auth()->id(),
            "updated_by" => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Data Barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = MasterItem::findOrFail($id);
        return view('master_items.view', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = MasterItem::findOrFail($id);
        return view('master_items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = MasterItem::findOrFail($id);
        $request->validate([
            'item_code' => 'required|string|unique:master_items,item_code,'.$item->id,
            'item_name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'sales_price' => 'required|integer|min:0',
            'item_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika ada file baru
        if ($request->hasFile('item_image')) {
            // Hapus foto lama jika ada
            if ($item->item_image && Storage::disk('public')->exists($item->item_image)) {
                Storage::disk('public')->delete($item->item_image);
            }

            // Simpan foto baru
            $newPhoto = $request->file('item_image')->store('item_images', 'public');
            $item->item_image = $newPhoto;
        }

        // Update data
        $item->item_code = $request->item_code;
        $item->item_name = $request->item_name;
        $item->stock = $request->stock; 
        $item->sales_price = $request->sales_price;
        $item->updated_by = auth()->id();
        $item->save();

        return redirect()->route('items.edit', $item->id)->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = MasterItem::findOrFail($id);
        $message = "Barang berhasil ";
        if ($item) {
            if ($item->is_transactional == 0 ) {
                $item->is_transactional = 1;
                $message .= "diaktifkan";
            } else {
                $item->is_transactional = 0;
                $message .= "dinonaktifkan";
            }
        }
        $item->update();

        return redirect()->back()->with('success', $message);
    }

    public function search(Request $request)
    {
        $search = $request->q;
 
        $items = MasterItem::where('item_name', 'like', "%$search%")
            ->orwhere('item_code', 'like', "%$search%")
            ->limit(50)
            ->get();

        return response()->json($items);
    }
}
