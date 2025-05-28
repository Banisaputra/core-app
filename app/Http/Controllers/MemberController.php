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
            'member_status' => 'string|max:50',
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
            'member_status' => $request->member_status,
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
            'member_status' => 'string|max:50',
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
        $member->member_status = $request->member_status;
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
        if($member && $user) {
            // delete picture
            if ($member->profile_photo && Storage::disk('public')->exists($member->profile_photo)) {
                Storage::disk('public')->delete($member->profile_photo);
            }
            $member->delete();
            $user->delete();
        }

        return redirect()->back()->with('success', "Data dan akun anggota berhasil dihapus");
        
    }
}
