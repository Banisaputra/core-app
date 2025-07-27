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
        $inventories = Inventory::all();
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

                        $stock->pushFifoBatch($available - $usedQty);
                        $remainQty -= $usedQty;
                    }
                    if ($remainQty > 0) {
                        throw new \Exception("Stock tidak cukup untuk item ID: $itemId", 1);
                    }

                    MasterItem::where('id', $itemId)->decrement('stock', $qty);
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
            return back()->with('error', 'Gagal menyimpan Inventory: Hubungi Administrator'.$e->getMessage())->withInput();
        }
    }
 
    public function show(string $id)
    {
        $inventory = Inventory::with(['invDetails.item'])->findOrFail($id);
        return view('inventories.view', compact('inventory'));
    }
}
