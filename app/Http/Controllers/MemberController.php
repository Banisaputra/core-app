<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Member;
use App\Models\Saving;
use App\Models\Devision;
use App\Models\Position;
use App\Models\SavingType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('user', 'position', 'devision')->latest()->paginate();
        return view('members.index', compact('members'));
    }

    public function create()
    {
        $devisions = Devision::all();
        $positions = Position::all();
        return view('members.create', compact('devisions','positions'));
    }

    public function store(Request $request)
    { 
        $request->validate([
            'nip' => 'required|integer',
            'position' => 'required|integer|exists:positions,id',
            'devision' => 'required|integer|exists:devisions,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'telphone' => 'required',
            'gender' => 'required|in:PRIA,WANITA',
            'no_kk' => 'required',
            'no_ktp' => 'required',
            'date_joined' => 'required|date',
            'address' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
 
        ]);

        // image path
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public_direct'); // public, if symlink not support use public_direct
        }
        // check generate account
        $password = Hash::make(Str::random(8));
        $email_verify = null;
        if(isset($request->accountGenerate)) {
            $password = Hash::make($request->email);
            $email_verify = now();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'email_verified_at' => $email_verify
        ]);
        $telphone = preg_replace('/[^0-9]/', '', $request->telphone);
        $member = Member::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'position_id' => $request->position,
            'devision_id' => $request->devision,
            'name' => $request->name,
            'email' => $request->email,
            'telphone' => $telphone,
            'gender' => $request->gender,
            'no_kk' => $request->no_kk,
            'no_ktp' => $request->no_ktp,
            'date_joined' => $request->date_joined,
            'image' => $photoPath,
            'address' => $request->address,
            "created_by" => auth()->id(),
            "updated_by" => auth()->id(),
        ]);

        $svn_code = Saving::generateCode();
        $pokok = SavingType::where('name', 'like', 'Pokok')->first();
        if ($pokok) {
            $svp=Saving::create([
                "sv_code" => $svn_code,
                'sv_date' => now()->format('Ymd'),
                'member_id' => $member->id,
                'sv_type_id' => $pokok->id,
                'sv_value' => $pokok->value,
                "proof_of_payment" => '',
                'remark' => 'Otomatis saat pendaftaran anggota',
                "created_by" => auth()->id(),
                "updated_by" => auth()->id(),
            ]);
        }
 
        if ($user && $member && $svp) {
            return redirect()->back()->with('success', 'Anggota baru berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem, hubungi administrator');
        }
    }

    public function show(string $id)
    {
        $member = Member::with('user')->findOrFail($id);
        $data = [
            "member" => $member,
            "roles" => Role::all(),
            "userRole" => Role::getUserRole($member->user_id)
        ];

        return view('members.view', $data);
    }

    public function edit(string $id)
    {
        $member = Member::with('user')->findOrFail($id);
        $devisions = Devision::all();
        $positions = Position::all();
        return view('members.edit', compact('member','devisions','positions'));
    }

    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        $user = User::findOrFail($member->user_id);
        $request->validate([
            'nip' => 'required|integer',
            'position' => 'required|integer|exists:positions,id',
            'devision' => 'required|integer|exists:devisions,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'telphone' => 'required',
            'gender' => 'required|in:PRIA,WANITA',
            'no_kk' => 'required',
            'no_ktp' => 'required',
            'date_joined' => 'required|date',
            'address' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika ada file baru
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($member->image && Storage::disk('public')->exists($member->image)) {
                Storage::disk('public')->delete($member->image);
            }

            // Simpan foto baru
            $newPhoto = $request->file('profile_photo')->store('profile_photos', 'public_direct');
            $member->image = $newPhoto;
        }

        // update user
        $user->name = $request->name;
        $user->email = $request->email;

        $telphone = preg_replace('/[^0-9]/', '', $request->telphone);

        // Update data
        $member->nip = $request->nip;
        $member->position_id = $request->position;
        $member->devision_id = $request->devision;
        $member->name = $request->name;
        $member->telphone = $telphone;
        $member->gender = $request->gender;
        $member->no_kk = $request->no_kk;
        $member->no_ktp = $request->no_ktp;
        $member->address = $request->address;
        $member->date_joined = $request->date_joined;
        $member->updated_by = auth()->id();
        $member->save();

        return redirect()->route('members.edit', $member->id)->with('success', 'Data anggota berhasil diperbarui.');

    }

    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $user = User::findOrFail($member->user_id);
        $message = "";
        if($member && $user) {
            // delete picture
            // if ($member->profile_photo && Storage::disk('public')->exists($member->profile_photo)) {
            //     Storage::disk('public')->delete($member->profile_photo);
            // }

            if ($member->is_transactional == 0 && $user->is_transactional == 0 ) {
                $member->is_transactional = 1;
                $user->is_transactional = 1;
                $message .= "diaktifkan";
            } else {
                $member->is_transactional = 0;
                $user->is_transactional = 0;
                $message .= "dinonaktifkan";
            }
        
            $member->update();
            $user->update();
        }

        return redirect()->back()->with('success', "Data dan akun anggota berhasil ".$message);
        
    }

    public function search(Request $request)
    {
        $search = $request->q;
        $is_active = $request->active;
        // Log::debug('Request Data:', $request->all());
        $whereAdd = "1=1";

        if ($is_active < 2) {
            $whereAdd = "is_transactional=".$is_active."";
        }

        $members = Member::when($search, function($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%");
            });
        })
        ->whereRAW($whereAdd)
        ->select('id', 'name', 'nip')
        ->limit(10)
        ->get();
        
        return response()->json($members);
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
                'nip' => $row[0] ?? null,
                'position' => $row[1] ?? null,
                'devision' => $row[2] ?? null,
                'name' => $row[3] ?? null,
                'email' => $row[4] ?? null,
                'telphone' => $row[5] ?? null,
                'gender' => $row[6] ?? null,
                'no_kk' => $row[7] ?? null,
                'no_ktp' => $row[8] ?? null,
                'date_joined' => $row[9] ?? null,
                'address' => $row[10] ?? null,
                'accountGenerate' => $row[11] ?? null,
            ];

           
            $mExists = Member::where('nip', $data['nip'])->first();
            $rules = $mExists ? 'exists' : 'unique';
            $validator = Validator::make($data, [
                'nip' => 'required|'.$rules.':members,nip',
                'position' => 'required|exists:positions,code',
                'devision' => 'required|exists:devisions,code',
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'telphone' => 'required',
                'gender' => 'required',
                'no_kk' => 'required|string|max:20',
                'no_ktp' => 'required|string|max:20',
                'date_joined' => 'required',
                'address' => 'required',
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }

            $position = Position::where('code', $data['position'])->first();
            $devision = Devision::where('code', $data['devision'])->first();

            // check generate account
            $password = Hash::make(Str::random(8));
            $email_verify = null;
            if($data['accountGenerate'] == "YA") {
                $password = Hash::make($request->email);
                $email_verify = now();
            }

            if ($mExists) {
                $user = User::findOrFails($mExists->user_id);
                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $password,
                    'email_verified_at' => $email_verify
                ]);

                $mExists->update([
                    'position_id' => $position->id,
                    'devision_id' => $devision->id,
                    'name' => $data['name'],
                    'telphone' => $data['telphone'],
                    'gender' => $data['gender'],
                    'no_kk' => $data['no_kk'],
                    'no_ktp' => $data['no_ktp'],
                    'address' => $data['address'],
                    'date_joined' => $data['date_joined'],
                    'updated_by' => auth()->id(),
                ]);
            } else {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $password,
                    'email_verified_at' => $email_verify
                ]);
    
                $member = Member::create([
                    'user_id' => $user->id,
                    'nip' => $data['nip'],
                    'position_id' => $position->id,
                    'devision_id' => $devision->id,
                    'name' => $data['name'],
                    'telphone' => $data['telphone'],
                    'gender' => $data['gender'],
                    'no_kk' => $data['no_kk'],
                    'no_ktp' => $data['no_ktp'],
                    'address' => $data['address'],
                    'date_joined' => $data['date_joined'],
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
        $sheet->setTitle('Template Master Anggota');

        // Judul besar
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'Template Master Anggota');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB0D5F6'],
            ],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header
        $headers = ['NIP', 'Kode Jabatan', 'Kode Bagian', 'Nama Lengkap', 'Email', 'No.Telp', 'Jenis Kelamin', 'No KK', 'No KTP', 'Tgl. Bergabung', 'Alamat', 'Akun Anggota'];
        $sheet->fromArray($headers, null, 'A2');

        // Style header
        $sheet->getStyle('A2:L2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);

        // Keterangan
        $notes = [
            "Harus diisi", "Sesuai dengan\nMaster Jabatan", "Sesuai dengan\nMaster Bagian",
            "Harus diisi", "Harus diisi", "Harus diisi",
            "PRIA / WANITA\nHarus diisi",
            "Harus diisi", "Harus diisi",
            "Format penulisan\nYYYY.MM.DD\nExp :\n - 2025.12.31\n - 2002.05.07",
            'Harus diisi', "YA / TIDAK\nYA untuk membuat account"
        ];

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        foreach ($notes as $index => $note) {
            $cell = chr(65 + $index) . '3'; // A3, B3, ...
            $sheet->setCellValue($cell, $note);
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB(Color::COLOR_RED);
            $sheet->getStyle($cell)->getAlignment()->setWrapText(true);
        }

        // Contoh data
        // $sheet->fromArray([
        //     ['5563463', 'Gunawan Putra', 'gunawan@gmail.com', '987689', '1999.04.15', 'PRIA', 'ISLAM', '2025.06.08', 'alamat gunawan sekarang sesuai ktp saja'],
        //     ['3453476', 'Dani Saputra', 'dani@gmail.com', '678799', '2002.05.14', 'PRIA', 'KRISTEN', '2025.06.08', 'alamat dani sekarang']
        // ], null, 'A4');

        // Generate response
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template-master-anggota.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            "Content-Type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Cache-Control" => "max-age=0",
        ]);
    }

    public function account(Request $request) 
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'email' => 'required|exists:users,email',
            'password' => 'nullable|min:8'
        ]);
        
        DB::beginTransaction();
        try {
            $member = Member::findOrFail($request->member_id);
            $user = User::where('email', $request->email)
            ->where('id', $member->user_id)
            ->first();

            if (isset($request->password) && $request->password != "") {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->update(['is_transactional' => isset($request->accountActive) ? 1 : 0]);
            
            if (isset($request->role) && $request->role != "") {
                if ($request->role > 0) {
                    $newRole = Role::findOrFail($request->role);
                    $newRole->asignRole($user->id);
                } else {
                    DB::table('role_user')->where('user_id', $user->id)
                    ->delete();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data Account berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan pembelian: ' . $e->getMessage())->withInput();
        }

    }

}
