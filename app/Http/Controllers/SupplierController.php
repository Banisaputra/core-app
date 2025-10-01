<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Supplier;
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

class SupplierController extends Controller
{
    public function index() {
        $supplier = Supplier::all();
        return view('supplier.index', compact('supplier'));
    }
 
    public function create() {
        return view('supplier.create');
    }

    public function store(Request $request)
    { 
        $request->validate([
            'code' => 'required|string|max:50|unique:suppliers,code',
            'name' => 'required|string|max:100',
            'address' => 'required|max:255'
        ], [
            'code.unique' => 'Kode sudah pernah digunakan.'
        ]); 
        $supplier = Supplier::create([
            'code' => $request->code,
            'name' => $request->name,
            'address' => $request->address,
            'is_active' => isset($request->is_active) ? 1 : 0,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($supplier) {
            return redirect()->back()->with('success', 'Supplier berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem, hubungi administrator');
        }
    }
   
    public function edit(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }
 
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $supplier = supplier::findOrFail($id);
        $request->validate([
            'code' => 'required|string|max:50|unique:suppliers,code,'. $id,
            'name' => 'required|string|max:100',
            'address' => 'required|max:255',
        ], [
            'code.unique' => 'Kode sudah pernah digunakan.'
        ]);

        $supplier->code = $request->code;
        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->is_active = 1;
        if (!isset($request->is_active)) {
            $supplier->is_active = 0;
        }

        $supplier->updated_by = auth()->id();
        $supplier->save();

        return redirect()->route('suppliers.edit', $supplier->id)->with('success', 'Data Supplier berhasil diperbarui.');

    }
 
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $message = "";
        if($supplier) { 
            if ($supplier->is_active == 0) {
                $supplier->is_active = 1;
                $message .= "diaktifkan";
            } else {
                $supplier->is_active = 0;
                $message .= "dinonaktifkan";
            }

            $supplier->update();
        }

        return redirect()->back()->with('success', "Supplier berhasil ".$message);
        
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $suppliers = Supplier::where('name', 'like', "%$search%")
            ->orwhere('code', 'like', "%$search%")
            ->select('id', 'name', 'code')
            ->limit(10)
            ->get();

        return response()->json($suppliers);
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
                'address' => $row[2] ?? null,
                'telphone' => $row[3] ?? null,
                'is_active' => $row[4] ?? null,
            ];

            $sExists = Supplier::where('code', $data['code'])->first();
            $rules = $sExists ? 'exists' : 'unique';

            $validator = Validator::make($data, [
                'code' => 'required|string|max:50|'.$rules.':suppliers,code',
                'name' => 'required|string|max:100',
                'telphone' => 'nullable|integer',
                'address' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }

            if($sExists) {
                $sExists->update([
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'telphone' => $data['telphone'],
                    'is_active' => $data['is_active'],
                    'updated_by' => auth()->id(),
                ]);
            } else {
                $supplier = Supplier::create([
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'telphone' => $data['telphone'],
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
        $sheet->setTitle('Template Master Supplier');

        // Judul besar
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'Template Master Supplier');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['Kode', 'Nama Supplier', 'Alamat', 'Telphone', 'Status'];
        $sheet->fromArray($headers, null, 'A2');

        // Style header
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Keterangan wajib isi
        $notes = [
            'Harus diisi', 'Harus diisi', 'Harus diisi', 'Opsional',
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
        $fileName = 'template-master-suppliers.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }

}
