<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index() 
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request) 
    {
        // validasi inputan
        $request->validate([
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.min' => 'Nama minimal 4 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.email' => 'format email tidak valid.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);
        
        // input data
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password)
        ]; 

        User::create($data);

        return response()->json(['success' => 'User pengguna baru berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:users,name,'.$id,
            'email' => 'required|string|max:100|unique:users,email,'.$id,
            'password' => 'nullable|min:8'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        if ($request->password != "") {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json([
            'success' => 'User berhasil diupdate.',
            'data' => $user
        ]);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if($user) {
            // delete
            $user->delete();
        }

        return redirect()->back()->with('success', "Data user berhasil dihapus.");
        
    }
}