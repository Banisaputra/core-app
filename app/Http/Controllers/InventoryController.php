<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ItemStock;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use App\Models\InventoryDetail;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            return back()->with('error', 'Gagal menyimpan Inventory! Hubungi Administrator')->withInput();
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
                // return back()->with('error', 'Gagal menyimpan Inventory: '.$e->getMessage())->withInput();
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
            return back()->with('error', 'Gagal menyimpan koreksi stok! Hubungi Administrator.')->withInput();
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

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Inventori Stok');

        // Judul besar
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Template Inventori Stok');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['Kode Barang', 'Tipe Stok', 'Keterangan', 'Jumlah'];
        $sheet->fromArray($headers, null, 'A2');

        // Style header
        $sheet->getStyle('A2:D2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Keterangan
        $notes = [
            'Harus diisi', "Harus diisi,\nHanya baris pertama\n- PENAMBAHAN\n- PENGURANGAN\n- OPERASIONAL", 
            "Harus diisi,\nHanya baris pertama\nyang tercatat", 'Harus diisi'
        ];

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        foreach ($notes as $index => $note) {
            $cell = chr(65 + $index) . '3'; // A3, B3, ...
            $sheet->setCellValue($cell, $note);
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
        }
 
        // Generate response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template-inventori-stok.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // ambil sample
        $header = $rows[3];
        
        $success = 0;
        $failed = [];
        $type_stock = "";
        $stock_remark = $header[2];
        
        switch ($header[1]) {
            case 'PENGURANGAN':
                $type_stock = "ADJUSTMENT OUT";
                break;
            case 'OPERASIONAL':
                $type_stock = "OPERATIONAL";
                break;
            default:
                $type_stock = "ADJUSTMENT IN";
                break;
        }

        DB::beginTransaction();
        try {
            $invCode = Inventory::generateCode();
            $inventory = Inventory::create([
                'code' => $invCode,
                'inv_date' => date('Ymd', strtotime(now())),
                'type' => $type_stock,
                'remark' => $stock_remark,
                'inv_state' => 1,
                "created_by" => auth()->id(),
                "updated_by" => auth()->id(),
            ]);

            foreach ($rows as $index => $row) {
                // cek template
                if ($index == 0) $template_title = strtoupper($row[0]);
                if ($template_title !== "TEMPLATE INVENTORI STOK") {
                    $failed[] = ['row' => $index + 1, 'errors' => ["Template tidak valid!"]];
                    break;
                } 
                if ($index <= 2) continue; // skip header and info 

                $data = [
                    'item_code' => $row[0] ?? null,
                    'type_stock' => $row[1] ?? null,
                    'remark' => $row[2] ?? null,
                    'amount' => $row[3] ?? null,
                ];

                $validator = Validator::make($data, [
                    'item_code' => 'required|string|max:50|exists:master_items,item_code',
                ]);
                
                $itemExists = MasterItem::where('item_code', $data['item_code'])->first();            
                if ($validator->fails()) {
                    $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                    // continue;
                    break;
                }

                if (strtoupper($type_stock) === "ADJUSTMENT IN") {
                    $batch = date('YmdHis');
                    InventoryDetail::create([
                        'inv_id' => $inventory->id,
                        'item_id' => $itemExists->id,
                        'amount' => $data['amount']*1,
                        'batch' => $batch,
                    ]);
                } else {
                    $batches = ItemStock::getFifoBatch($itemExists->id*1);
                    if ($batches->isEmpty()) {
                        $failed[] = ['row' => $index + 1, 'errors' => [["Stock Barang tidak tersedia!"]]];
                        break;
                    }

                    // calculate stock
                    $remainQty = $data['amount']*1;
                    foreach ($batches as $stock) {
                        if ($remainQty == 0) break;

                        $available = $stock->remaining_stock;
                        $usedQty = min($available, $remainQty);

                        InventoryDetail::create([
                            'inv_id' => $inventory->id,
                            'item_id' => $itemExists->id,
                            'amount' => $usedQty,
                            'batch' => $stock->batch,
                        ]);

                        $remainQty -= $usedQty;
                    }

                    if ($remainQty > 0) {
                        $failed[] = ['row' => $index + 1, 'errors' => [["Stock tidak cukup untuk item Code: $itemExists->item_code"]]];
                        break;
                    }
                }

                $success++;
            }
           
            DB::commit();
            return redirect()->back()->with('success', "$success data berhasil diimport")->with('failed', $failed);
        } catch (\Exception $e) {
            DB::rollback();
            // $e->getMessage(); 
            return redirect()->back()->with('success', "$success data berhasil diimport ".$e->getMessage()."")->with('failed', $failed);

        }
    }
}
