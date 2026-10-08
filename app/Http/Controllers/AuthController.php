<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi berbasis Kode Akses saja
     */
    public function login(Request $request)
    {
        $request->validate([
            'kode_akses' => 'required|string',
        ], [
            'kode_akses.required' => 'Masukkan kode akses Anda terlebih dahulu.',
        ]);

        // Cari user dalam database
        // Jika hanya 1 user pribadi, kita ambil user pertama
        $user = User::first();

        if ($user && Hash::check($request->kode_akses, $user->kode_akses)) {
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'kode_akses' => 'Kode akses yang Anda masukkan salah.',
        ])->onlyInput('kode_akses');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
