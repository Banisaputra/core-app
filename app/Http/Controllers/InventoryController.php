<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use App\Models\InventoryDetail;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index() {
        $inventories = Inventory::orderby('id', 'DESC')->get();
        return view('inventories.index', compact('inventories')); 
    }

    public function create() {
        $inv_code = Inventory::generateCode();
        return view('inventories.create', compact('inv_code'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inv_date' => 'required|date',
            'type' => 'required',
            'remark' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:master_items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $invCode = Inventory::generateCode();
            $inventory = Inventory::create([
                'code' => $invCode,
                'inv_date' => date('Ymd', strtotime($validated['inv_date'])),
                'type' => $validated['type'],
                'remark' => $validated['remark'],
                'inv_state' => 1,
                "created_by" => auth()->id(),
                "updated_by" => auth()->id(),
            ]);
            
            foreach ($validated['items'] as $item) {
                $itemId = $item['item_id'];
                $qty = $item['qty'];

                if ($validated['type'] == 'ADJUSTMENT IN') {
                    $batch = date('YmdHis');
                    InventoryDetail::create([
                        'inv_id' => $inventory->id,
                        'item_id' => $itemId,
                        'amount' => $qty,
                        'batch' => $batch,
                    ]);

                } else {
                    $batches = ItemStock::getFifoBatch($itemId);
                    if ($batches->isEmpty()) {
                        throw new \Exception("Stock Barang tidak tersedia!", 1);
                    }

                    // calculate stock
                    $remainQty = $qty;
                    foreach ($batches as $stock) {
                        if ($remainQty == 0) break;

                        $available = $stock->remaining_stock;
                        $usedQty = min($available, $remainQty);

                        InventoryDetail::create([
                            'inv_id' => $inventory->id,
                            'item_id' => $itemId,
                            'amount' => $usedQty,
                            'batch' => $stock->batch,
                        ]);

                        $remainQty -= $usedQty;
                    }
                    if ($remainQty > 0) {
                        throw new \Exception("Stock tidak cukup untuk item ID: $itemId", 1);
                    }
                }
            }

            DB::commit();
            return redirect()->route('inv.index')->with('success', 'Koreksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            // $e->getMessage();
            if ($e->getCode() == 1) {
                return back()->with('error', 'Gagal menyimpan Inventory: '.$e->getMessage())->withInput();
            }
            return back()->with('error', 'Gagal menyimpan Inventory: Hubungi Administrator')->withInput();
        }
    }
 
    public function show(string $id)
    {
        $inventory = Inventory::with(['invDetails.item'])->findOrFail($id);
        $invDetails = InventoryDetail::with('item')
            ->where('inv_id', $id)
            ->get()
            ->groupBy('item_id')
            ->map(function ($items, $itemId) {
                return [
                    'item_id' => $itemId,
                    'item_code' => $items->first()->item->item_code ?? '',
                    'item_name' => $items->first()->item->item_name ?? '',
                    'total_qty' => $items->sum('amount'),
                    'batches' => $items->map(function ($i) {
                        return [
                            'batch' => $i->batch,
                            'amount' => $i->amount,
                        ];
                    }),
                ];
        });

        return view('inventories.view', compact('inventory','invDetails'));
    }

    public function edit(string $id)
    {
        $inventory = Inventory::with(['invDetails.item'])->findOrFail($id);
        $invDetails = InventoryDetail::with('item')
            ->where('inv_id', $id)
            ->get()
            ->groupBy('item_id')
            ->map(function ($items, $itemId) {
                return [
                    'item_id' => $itemId,
                    'item_code' => $items->first()->item->item_code ?? '',
                    'item_name' => $items->first()->item->item_name ?? '',
                    'item_stock' => $items->first()->item->stock ?? 0,
                    'total_qty' => $items->sum('amount'),
                    'batches' => $items->map(function ($i) {
                        return [
                            'batch' => $i->batch,
                            'amount' => $i->amount,
                        ];
                    }),
                ];
        });

        return view('inventories.edit', compact('inventory', 'invDetails'));
    }
 
    public function update(Request $request, $id)
    {
         
        $validated = $request->validate([
            'inv_date' => 'required|date',
            'remark' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:master_items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $inventory = Inventory::findOrFail($id);
            $request['inv_date'] = date('Ymd', strtotime($request['inv_date']));
            $inventory->update($request->only('code', 'inv_date', 'remark'));

            InventoryDetail::where('inv_id', $inventory->id)
            ->delete();
            
            foreach ($validated['items'] as $item) {
                $itemId = $item['item_id'];
                $qty = $item['qty'];

                if ($inventory->type == 'ADJUSTMENT IN') {
                    $batch = date('YmdHis');
                    InventoryDetail::create([
                        'inv_id' => $inventory->id,
                        'item_id' => $itemId,
                        'amount' => $qty,
                        'batch' => $batch,
                    ]);

                } else {
                    $batches = ItemStock::getFifoBatch($itemId);
                    if ($batches->isEmpty()) {
                        throw new \Exception("Stock Barang tidak tersedia!", 1);
                    }

                    // calculate stock
                    $remainQty = $qty;
                    foreach ($batches as $stock) {
                        if ($remainQty == 0) break;

                        $available = $stock->remaining_stock;
                        $usedQty = min($available, $remainQty);

                        InventoryDetail::create([
                            'inv_id' => $inventory->id,
                            'item_id' => $itemId,
                            'amount' => $usedQty,
                            'batch' => $stock->batch,
                        ]);

                        $remainQty -= $usedQty;
                    }
                    if ($remainQty > 0) {
                        throw new \Exception("Stock tidak cukup untuk item ID: $itemId", 1);
                    }
                }
            }
                
            DB::commit();
            return redirect()->route('inv.index')->with('success', 'Koreksi berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollback();
            // $e->getMessage();
            if ($e->getCode() == 1) {
                return back()->with('error', 'Gagal menyimpan Inventory: '.$e->getMessage())->withInput();
            }
            return back()->with('error', 'Gagal menyimpan Inventory: Hubungi Administrator')->withInput();
        }
    }

    public function confirmation(Request $request) 
    {
        $request->validate([
            'inv_id' => 'required|exists:inventories,id'
        ]);

        $inventory = Inventory::with(['invDetails.item'])->findOrFail($request->inv_id);
        if ($inventory->inv_state != 1) return redirect()->back()->with('error', 'Dokumen Inventory tidak valid, atau sudah pernah dikonfirmasi');

        DB::beginTransaction();
        try {
            foreach ($inventory->invDetails as $item) {
                $itemId = $item['item_id'];
                $qty = $item['amount'];
                if ($inventory->type == "ADJUSTMENT IN") {
                    $batch = date('YmdHis');

                    $lastBatch = ItemStock::getFifoBatch($itemId, 'desc')->first();
                    ItemStock::create([
                        'item_id' => $itemId,
                        'batch' => $batch,
                        'stock' => $qty,
                        'price' => $lastBatch->price ?? 0,
                        'remaining_stock' => $qty,
                        'ref_doc_id' => $inventory->id,
                        'ref_doc_type' => 'INVENTORY'
                    ]);
                    MasterItem::where('id', $itemId)->increment('stock', $qty);
                } else {

                    $batchStock = ItemStock::where('item_id', $itemId)
                        ->where('batch', $item['batch'])
                        ->first();
                    // dd($batchStock);
                    if ($batchStock->remaining_stock < $qty) return redirect()->back()->with('error', 'Stok Barang Inventory tidak mencukupi!');
                    $batchStock->pushFifoBatch($batchStock->remaining_stock - $qty);
                    
                    // if out
                    MasterItem::where('id', $itemId)->decrement('stock', $qty);
                }
            }
            $inventory->update([
                'inv_state' => 2,
                'updated_by' => auth()->id()
            ]);
                
            DB::commit();
            return redirect()->back()->with('success', 'Koreksi Inventory berhasil dikonfirmasi');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan koreksi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'inv_id' => 'required|exists:inventories,id'
        ]);

        $inventory = Inventory::with(['invDetails.item'])->findOrFail($request->inv_id);
        $inventory->update([
            'inv_state' => 99,
            'updated_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Koreksi barang berhasil dibatalkan.');
    }

}
