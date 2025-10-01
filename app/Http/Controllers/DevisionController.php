<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Devision;
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

class DevisionController extends Controller
{
    public function index() {
        $devision = Devision::all();
        return view('devision.index', compact('devision'));
    }
 
    public function create() {
        return view('devision.create');
    }

    public function store(Request $request)
    { 
        $request->validate([
            'code' => 'required|string|max:50|unique:devisions,code',
            'name' => 'required|string|max:100',
            'remark' => 'nullable'
        ], [
            'code.unique' => 'Kode sudah pernah digunakan'
        ]);
        
        $devision = Devision::create([
            'code' => $request->code,
            'name' => $request->name,
            'remark' => $request->remark,
            'is_transactional' => isset($request->is_active) ? 1 : 0,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($devision) {
            return redirect()->back()->with('success', 'Bagian baru berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem, hubungi administrator');
        }
    }
   
    public function edit(string $id)
    {
        $devision = Devision::findOrFail($id);
        return view('devision.edit', compact('devision'));
    }
 
    public function update(Request $request, string $id)
    {
        $devision = Devision::findOrFail($id);
        $request->validate([
            'code' => 'required|string|max:50|unique:devisions,code,'. $id,
            'name' => 'required|string|max:100',
            'remark' => 'nullable'
        ], [
            'code.unique' => 'Kode sudah pernah digunakan.'
        ]);

        $devision->code = $request->code;
        $devision->name = $request->name;
        $devision->remark = $request->remark;
        $devision->is_transactional = 1;
        if (!isset($request->is_active)) {
            $devision->is_transactional = 0;
        }
        $devision->save();

        return redirect()->route('devisions.edit', $devision->id)->with('success', 'Data Kategori berhasil diperbarui.');

    }
 
    public function destroy(string $id)
    {
        $devision = Devision::findOrFail($id);
        $message = "";
        if($devision) { 
            if ($devision->is_transactional == 0) {
                $devision->is_transactional = 1;
                $message .= "diaktifkan";
            } else {
                $devision->is_transactional = 0;
                $message .= "dinonaktifkan";
            }
        
            $devision->update();
        }

        return redirect()->back()->with('success', "Bagian berhasil ".$message);
        
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $devisions = Devision::where('name', 'like', "%$search%")
            ->orwhere('code', 'like', "%$search%")
            ->select('id', 'name', 'code')
            ->limit(10)
            ->get();

        return response()->json($devisions);
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
            if ($template_title !== "TEMPLATE MASTER BAGIAN") {
                $failed[] = ['row' => $index + 1, 'errors' => ["Template tidak valid"]];
                break;
            } 
            if ($index <= 2) continue; // skip header and info 
            $data = [
                'code' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'remark' => $row[2] ?? null,
                'is_transactional' => $row[3] ?? null,
            ];

            $dExists = Devision::where('code', $data['code'])->first();
            $rules = $dExists ? 'exists' : 'unique';

            $validator = Validator::make($data, [
                'code' => 'required|string|max:50|'.$rules.':devisions,code',
                'name' => 'required|string|max:100',
                'remark' => 'nullable'
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }             

            if ($dExists) {
                $dExists->update([
                    'name' => $data['name'],
                    'remark' => $data['remark'],
                    'is_transactional' => $data['is_transactional'],
                ]);

            } else {
                $devision = Devision::create([
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'remark' => $data['remark'],
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
        $sheet->setTitle('Template Master Bagian');

        // Judul besar
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Template Master Bagian');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['Kode', 'Nama Kategori', 'Keterangan', 'Status'];
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
        $fileName = 'template-master-devision.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }

}
