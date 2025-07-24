<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index() {
        $purchases = Purchase::all();
        return view('purchases.index', compact('purchases'));
    }

    public function create() {
        $data = [
            "pr_code" => Purchase::generateCode()
        ];
        return view('purchases.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ref_doc' => 'required|unique:purchases,ref_doc',
            'pr_date' => 'required|date',
            'supplier' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:master_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'invoice_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // image path
        $photoPath = null;
        if ($request->hasFile('invoice_file')) {
            $photoPath = $request->file('invoice_file')->store('invoice_images', 'public');
        }

        DB::beginTransaction();
        try {
            // Hitung total
            $total = collect($validated['items'])->sum(function ($item) {
                return $item['qty'] * $item['price'];
            });

            $pr_code = Purchase::generateCode();

            // Simpan ke tabel purchases
            $purchase = Purchase::create([
                'ref_doc' => $validated['ref_doc'],
                'pr_code' => $pr_code,
                'pr_date' => date('Ymd', strtotime($validated['pr_date'])),
                'supplier' => $validated['supplier'],
                'total' => $total,
                'file_path' => $photoPath,
                "created_by" => auth()->id(),
                "updated_by" => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseDetail::create([
                    'pr_id' => $purchase->id,
                    'item_id' => $item['item_id'],
                    'amount' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['qty'],
                ]);

                // Tambah stok
                MasterItem::where('id', $item['item_id'])->increment('stock', $item['qty']);
            }
                
            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage())->withInput();
        }
    }
 
    public function show(string $id)
    {
        $purchase = Purchase::with(['prDetails.item'])->findOrFail($id);
        return view('purchases.view', compact('purchase'));
    }
 
    public function edit(string $id)
    {
        $purchase = Purchase::with(['prDetails.item'])->findOrFail($id);
        return view('purchases.edit', compact('purchase'));
    }
 
    public function update(Request $request, $id)
    {
         
        $request->validate([
            'ref_doc' => 'required|unique:purchases,ref_doc,'. $id,
            'pr_date' => 'required|date',
            'supplier' => 'required',
            'items.*.item_id' => 'required|exists:master_items,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'invoice_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);
        // image path
        $photoPath = null;
        if ($request->hasFile('invoice_file')) {
            $photoPath = $request->file('invoice_file')->store('invoice_image', 'public');
        }

        $total = collect($request['items'])->sum(function ($item) {
            return $item['qty'] * $item['price'];
        });
        $request['total'] = $total;
        $request['pr_date'] = date('Ymd', strtotime($request['pr_date']));
        $purchase = Purchase::findOrFail($id);
        $purchase->update($request->only('ref_doc', 'pr_date', 'supplier', 'total'));
        if ($request->hasFile('invoice_file') && $photoPath !== null) {
            $purchase->update(['filepath' => $photoPath]);
        }

        $purchase->prDetails()->delete();

        foreach ($request->items as $item) {
            $purchase->prDetails()->create([
                'pr_id' => $purchase->id,
                'item_id' => $item['item_id'],
                'amount' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['qty'],
            ]);
        }

        return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil diperbarui');
    }
 
    public function destroy(string $id)
    {
        // code...
    }

}
