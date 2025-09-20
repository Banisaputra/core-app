<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CategoryController extends Controller
{
    public function index() {
        $category = Category::all();
        return view('category.index', compact('category'));
    }
 
    public function create() {
        $ct_parent = Category::where('is_parent', 1)->get();
        return view('category.create', compact('ct_parent'));
    }

    public function store(Request $request)
    { 
        $request->validate([
            'code' => 'required|string|max:50|unique:categories,code',
            'name' => 'required|string|max:100',
        ], [
            'code.unique' => 'Kode sudah pernah digunakan'
        ]); 
        
        $is_parent = 1;
        $parent = 0;
        if (isset($request->is_turunan)) {
            $request->validate([
                'ct_parent' => 'required|exists:categories,id',
            ], [
                'ct_parent.required' => 'Kategori utama harus dipilih',
                'ct_parent.exist' => 'Kategori utama tidak ditemukan'
            ]);

            $is_parent = 0;
            $parent = $request->ct_parent;
        }

        $category = Category::create([
            'code' => $request->code,
            'name' => $request->name,
            'ppn_percent' => $request->ppn_percent ?? 0,
            'margin_percent' => $request->margin_percent ?? 0,
            'margin_price' => $request->margin_price ?? 0,
            'parent_id' => $parent,
            'is_parent' => $is_parent,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($category) {
            return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem, hubungi administrator');
        }
    }
   
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $ct_parent = Category::where('is_parent', 1)->get();

        return view('category.edit', compact('category', 'ct_parent'));
    }
 
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'code' => 'required|string|max:50|unique:categories,code,'. $id,
            'name' => 'required|string|max:100',
        ], [
            'code.unique' => 'Kode sudah pernah digunakan.'
        ]);

        $is_parent = 1;
        $parent = 0;
        if (isset($request->is_turunan)) {
            $request->validate([
                'ct_parent' => 'required|exists:categories,id',
            ], [
                'ct_parent.required' => 'Kategori utama harus dipilih',
                'ct_parent.exist' => 'Kategori utama tidak ditemukan'
            ]);

            $is_parent = 0;
            $parent = $request->ct_parent;
        }

        $category->code = $request->code;
        $category->name = $request->name;
        $category->ppn_percent = $request->ppn_percent ?? 0;
        $category->margin_percent = $request->margin_percent ?? 0;
        $category->margin_price = $request->margin_price ?? 0;
        $category->is_parent = $is_parent;
        $category->parent_id = $parent;
        $category->updated_by = auth()->id();
        $category->save();

        return redirect()->route('category.edit', $category->id)->with('success', 'Data Kategori berhasil diperbarui.');

    }
 
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $message = "";
        if($category) { 
            if ($category->is_active == 0) {
                $category->is_active = 1;
                $message .= "diaktifkan";
            } else {
                $category->is_active = 0;
                $message .= "dinonaktifkan";
            }
        
            $category->update();
        }

        return redirect()->back()->with('success', "Kategori berhasil ".$message);
        
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $categories = Category::where('name', 'like', "%$search%")
            ->orwhere('code', 'like', "%$search%")
            ->select('id', 'name', 'code')
            ->limit(10)
            ->get();

        return response()->json($categories);
    }

    public function getMargin(string $id)
    {
        $category = Category::findOrFail($id);
        $margin = $category->getMargin();
        return response()->json($margin);

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
            $data = [
                'code' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'margin' => $row[2] ?? null,
                'is_parent' => $row[3] ?? null,
                'is_active' => $row[4] ?? null,
            ];

            $cExists = Category::where('code', $data['code'])->first();
            $rules = $cExists ? 'exists' : 'unique';

            $validator = Validator::make($data, [
                'code' => 'required|string|max:50|'.$rules.':categories,code',
                'name' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }             

            if ($cExists) {
                $cExists->update([
                    'name' => $data['name'],
                    'margin' => $data['margin'],
                    'is_active' => $data['is_active'],
                    'updated_by' => auth()->id(),
                ]);

            } else {
                $category = Category::create([
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'is_active' => $data['is_active'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
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
        $sheet->setTitle('Template Master Kategori');

        // Judul besar
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'Template Master Kategori');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['Kode', 'Nama Kategori', 'Margin Penjualan (%)', 'Tipe', 'Status'];
        $sheet->fromArray($headers, null, 'A2');

        // Style header
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Keterangan wajib isi
        $notes = [
            'Harus diisi', 'Harus diisi', 'Opsional',
            "UTAMA = 1\nTURUNAN = 0",
            "Aktif = 1\nTidak Aktif = 0",
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
        $fileName = 'template-master-categories.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }



}
