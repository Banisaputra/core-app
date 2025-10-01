<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Position;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PositionController extends Controller
{
    public function index() {
        $positions = Position::all();
        return view('positions.index', compact('positions'));
    }
 
    public function create() {
        return view('positions.create');
    }

    public function store(Request $request)
    { 
        $request->validate([
            'code' => 'required|string|max:50|unique:positions,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable'
        ], [
            'code.unique' => 'Kode sudah pernah digunakan'
        ]); 
        
        $position = Position::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'is_transactional' => isset($request->is_active) ? 1 : 0,
        ]);

        if ($position) {
            return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem, hubungi administrator');
        }
    }
   
    public function edit(string $id)
    {
        $position = Position::findOrFail($id);
        return view('positions.edit', compact('position'));
    }
 
    public function update(Request $request, string $id)
    {
        $position = Position::findOrFail($id);
        $request->validate([
            'code' => 'required|string|max:50|unique:positions,code,'. $id,
            'name' => 'required|string|max:100',
            'description' => 'nullable'
        ], [
            'code.unique' => 'Kode sudah pernah digunakan.'
        ]);

        $position->code = $request->code;
        $position->name = $request->name;
        $position->description = $request->description;
        $position->is_transactional = 1;
        if (!isset($request->is_active)) {
            $position->is_transactional = 0;
        }
        $position->save();

        return redirect()->route('positions.edit', $position->id)->with('success', 'Data Jabatan berhasil diperbarui.');

    }
 
    public function destroy(string $id)
    {
        $position = Position::findOrFail($id);
        $message = "";
        if($position) { 
            if ($position->is_transactional == 0) {
                $position->is_transactional = 1;
                $message .= "diaktifkan";
            } else {
                $position->is_transactional = 0;
                $message .= "dinonaktifkan";
            }
        
            $position->update();
        }

        return redirect()->back()->with('success', "Jabatan berhasil ".$message);
        
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $positions = Position::where('name', 'like', "%$search%")
            ->orwhere('code', 'like', "%$search%")
            ->select('id', 'name', 'code')
            ->limit(10)
            ->get();

        return response()->json($positions);
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
            // cek template
            if ($index == 0) $template_title = strtoupper($row[0]);
            if ($template_title !== "TEMPLATE MASTER JABATAN") {
                $failed[] = ['row' => $index + 1, 'errors' => ["Template tidak valid"]];
                break;
            } 
            if ($index <= 2) continue; // skip header and info 
            $data = [
                'code' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'description' => $row[2] ?? null,
                'is_transactional' => $row[3] ?? null,
            ];

            $cExists = Position::where('code', $data['code'])->first();
            $rules = $cExists ? 'exists' : 'unique';

            $validator = Validator::make($data, [
                'code' => 'required|string|max:50|'.$rules.':positions,code',
                'name' => 'required|string|max:100',
                'description' => 'nullable'
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            } 

            if ($cExists) {
                $cExists->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_transactional' => $data['is_transactional'],

                ]);

            } else {
                $position = position::create([
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_transactional' => $data['is_transactional'],
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
        $sheet->setTitle('Template Master Jabatan');

        // Judul besar
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Template Master Jabatan');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['Kode', 'Nama Jabatan', 'Deskripsi', 'Status'];
        $sheet->fromArray($headers, null, 'A2');

        // Style header
        $sheet->getStyle('A2:D2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Keterangan wajib isi
        $notes = [
            'Harus diisi', 'Harus diisi', 'Opsional',
            "Aktif = 1\nTidak Aktif = 0",
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
        $fileName = 'template-master-positions.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }



}
