<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request) 
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.'
        ]);

        $credentials = $request->only('email', 'password');
        $is_valid = Auth::attempt($credentials);

        if ($is_valid) {
            $user = Auth::user();
            
            if ($user->is_transactional == 1) {
                $request->session()->regenerate();
                return redirect()->intended('/');
            } else {
                // Logout user karena status tidak aktif
                Auth::logout();
                
                return redirect()->back()
                    ->withErrors([
                        'credentials' => 'Account belum teraktivasi, hubungi administrator.'
                    ])
                    ->onlyInput('email');
            }
        } else {
            return redirect()->back()
                ->withErrors([
                    'credentials' => 'Email atau password salah.'
                ])
                ->onlyInput('email');
        }

        return back()->withErrors([
            'credentials' => 'Email atau Password tidak valid.'
        ])->onlyInput('email');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register (Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $role = Role::where('name', 'like', 'Member')->first();
            DB::table('role_user')
            ->insert([
                'user_id' => $user->id,
                'role_id' => $role->id
            ]); 
    
            session(['user' => $user]);
            DB::commit();
            return redirect('/login')->with('success', 'Registrasi berhasil, silahkan login');
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
            return redirect()->back()->with('error', 'Terjadi kesalahan, Hubungi administrator');
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
