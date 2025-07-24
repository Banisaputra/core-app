<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;


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
            $photoPath = $request->file('item_image')->store('item_images', 'public');
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
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($items);
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

        $success = 0;
        $failed = [];

        foreach ($rows as $index => $row) {
            if ($index <= 2) continue; // skip header and info 

            // check category
            $category = Category::where('name', $row[2])->first();
            $categoryId = $category ? $category->id : 0;
            $data = [
                'item_code' => $row[0] ?? null,
                'item_name' => $row[1] ?? null,
                'ct_id' => $categoryId ?? null,
                'stock' => $row[3] ?? null,
                'sales_price' => $row[4] ?? null,
            ];

            // check item
            $itemExists = MasterItem::where('item_code', $data['item_code'])->first();

            $rules = $itemExists ? 'exists' : 'unique';             

            $validator = Validator::make($data, [
                'item_code' => 'required|string|max:50|'.$rules.':master_items,item_code',
                'item_name' => 'required|string|max:100',
                'ct_id' => 'required|integer|exists:categories,id',
                'stock' => 'required',
                'sales_price' => 'required',
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }

            if ($itemExists) {
                $itemExists->update([
                    "item_name" => $data['item_name'],
                    "ct_id" => $categoryId,
                    "stock" => $data['stock'],
                    "sales_price" => $data['sales_price'],
                    "item_image" => "",
                    "updated_by" => auth()->id(),
                ]);
            } else {
                $item = MasterItem::create([
                    "item_code" => $data['item_code'],
                    "item_name" => $data['item_name'],
                    "ct_id" => $categoryId,
                    "stock" => $data['stock'],
                    "sales_price" => $data['sales_price'],
                    "item_image" => "",
                    "created_by" => auth()->id(),
                    "updated_by" => auth()->id(),
                ]);
            }

            $success++;
        }

        return redirect()->back()->with('success', "$success data berhasil diimport")->with('failed', $failed);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Master Barang');

        // Judul besar
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'Template Master Barang');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['Kode Barang', 'Nama Barang', 'Kategori', 'Stok', 'Harga Jual'];
        $sheet->fromArray($headers, null, 'A2');

        // Style header
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Keterangan
        $notes = [
            'Harus diisi', 'Harus diisi', "Harus sesuai\nMaster Kategori", '0 atau lebih besar', "Tulisakan hanya angka"
        ];

        foreach (range('A', 'E') as $col) {
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
        $fileName = 'template-master-barang.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }

}
