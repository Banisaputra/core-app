<?php

namespace App\Http\Controllers;

use Log;
use Exception;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with('user')->latest()->paginate();
        return view('members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'nip' => 'integer',
            'employment' => 'string',
            'telphone' => 'required|integer',
            'gender' => 'required|in:female,male',
            'religion' => 'required|in:Islam,Kristen,Katholik,Hindu,Budha',
            'balance' => 'integer',
            'date_joined' => 'required|date',
            'address' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // image path
        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
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
        $member = Member::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'name' => $request->name,
            'telphone' => $request->telphone,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'employment' => $request->employment,
            'address' => $request->address,
            'image' => $photoPath,
            'balance' => $request->balance,
            'date_joined' => $request->date_joined,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        if ($user && $member) {
            return redirect()->back()->with('success', 'Anggota baru berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem, hubungi administrator');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::with('user')->findOrFail($id);
        return view('members.view', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = Member::with('user')->findOrFail($id);
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        $user = User::findOrFail($member->user_id);
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'date_of_birth' => 'required|date',
            'nip' => 'integer',
            'employment' => 'string',
            'telphone' => 'required|integer',
            'gender' => 'required|in:female,male',
            'religion' => 'required|in:Islam,Kristen,Katholik,Hindu,Budha',
            'balance' => 'integer',
            'date_joined' => 'required|date',
            'address' => 'required',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Jika ada file baru
        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($member->profile_photo && Storage::disk('public')->exists($member->profile_photo)) {
                Storage::disk('public')->delete($member->profile_photo);
            }

            // Simpan foto baru
            $newPhoto = $request->file('profile_photo')->store('profile_photos', 'public');
            $member->profile_photo = $newPhoto;
        }

        // update user
        $user->name = $request->name;
        $user->email = $request->email;

        // Update data
        $member->name = $request->name;
        $member->nip = $request->nip;
        $member->telphone = $request->telphone;
        $member->religion = $request->religion;
        $member->gender = $request->gender;
        $member->date_of_birth = $request->date_of_birth;
        $member->employment = $request->employment;
        $member->address = $request->address;
        $member->balance = $request->balance;
        $member->date_joined = $request->date_joined;
        $member->updated_by = auth()->id();
        $member->save();

        return redirect()->route('members.edit', $member->id)->with('success', 'Data anggota berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
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

        $members = Member::where('name', 'like', "%$search%")
            ->orwhere('nip', 'like', "%$search%")
            ->select('id', 'name')
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
                'name' => $row[1] ?? null,
                'email' => $row[2] ?? null,
                'telphone' => $row[3] ?? null,
                'date_of_birth' => $row[4] ?? null,
                'gender' => $row[5] ?? null,
                'religion' => $row[6] ?? null,
                'date_joined' => $row[7] ?? null,
                'address' => $row[8] ?? null,
            ];

            $validator = Validator::make($data, [
                'nip' => 'nullable',
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'date_of_birth' => 'required',
                'telphone' => 'required',
                'gender' => 'required',
                'religion' => 'required',
                'date_joined' => 'required',
                'address' => 'required',
            ]);

            if ($validator->fails()) {
                $failed[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }

            $password = Hash::make($request->email);
            $email_verify = now();
            

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
                'email_verified_at' => $email_verify
            ]);

            $member = Member::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'name' => $data['name'],
                'telphone' => $data['telphone'],
                'religion' => $data['religion'],
                'gender' => $data['gender'],
                'date_of_birth' => $data['date_of_birth'],
                'address' => $data['address'],
                'date_joined' => $data['date_joined'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $success++;
        }

        return back()->with('success', "$success data berhasil diimport")->with('failed', $failed);
    }

}
