<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\ItemStock;
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
            'ref_doc' => 'required',
            'pr_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'over_due' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:master_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'invoice_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // image path
        $photoPath = '';
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
            $batch = date('YmdHis');
            // Simpan ke tabel purchases
            $purchase = Purchase::create([
                'ref_doc' => $request->ref_doc,
                'pr_code' => $pr_code,
                'pr_date' => date('Ymd', strtotime($validated['pr_date'])),
                'supplier_id' => $validated['supplier_id'],
                'total' => $total,
                'file_path' => $photoPath,
                'is_finished' => 0,
                'over_due' => date('Ymd', strtotime($validated['over_due'])),
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
                    'batch' => $batch
                ]);

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
        $purchase = Purchase::with(['prDetails.item', 'supplier'])->findOrFail($id);
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
            'supplier_id' => 'required|exists:suppliers,id',
            'over_due' => 'required|date',
            'items.*.item_id' => 'required|exists:master_items,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'invoice_file' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $batch = date('YmdHis');
        DB::beginTransaction();
        try {
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
            $request['over_due'] = date('Ymd', strtotime($request['over_due']));
            $purchase = Purchase::findOrFail($id);

            if ($purchase->pr_state != 1) {
                return redirect()->back()->with('error', 'Dokumen pembelian tidak valid atau sudah dikonfirmasi');
            }

            $purchase->update($request->only('ref_doc', 'pr_date', 'supplier_id', 'total', 'over_due'));
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
                    'batch' => $batch
                ]);
            }
            $purchase->update([
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage())->withInput();
        }
    }
 
    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'pr_id' => 'required|exists:purchases,id'
        ]);

        $purchase = Purchase::with(['prDetails.item'])->findOrFail($request->pr_id);
        $purchase->update([
            'pr_state' => 99,
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Pembelian berhasil dibatalkan.');
    }

    public function confirmation(Request $request) 
    {
        $request->validate([
            'pr_id' => 'required|exists:purchases,id'
        ]);

        $purchase = Purchase::with(['prDetails.item'])->findOrFail($request->pr_id);
        if ($purchase->pr_state != 1) return redirect()->back()->with('error', 'Dokumen pembelian tidak valid, atau sudah pernah dikonfrimasi');

        DB::beginTransaction();
        try {
            foreach ($purchase->prDetails as $item) {
                MasterItem::where('id', $item['item_id'])->increment('stock', $item['amount']);
                ItemStock::create([
                    'item_id' => $item['item_id'],
                    'batch' => $item['batch'],
                    'stock' => $item['amount'],
                    'price' => $item['price'],
                    'remaining_stock' => $item['amount'],
                    'ref_doc_id' => $purchase->id,
                    'ref_doc_type' => 'PURCHASE'
                ]);

            }
            $purchase->update([
                'pr_state' => 2,
                'updated_by' => auth()->id()
            ]);
                
            DB::commit();
            return redirect()->back()->with('success', 'Pembelian berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage())->withInput();
        }
    }

}
