<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login khusus pelanggan/front
    public function showAuthPage() {
        // Jika user sudah login, langsung lempar ke halaman utama
        if (Auth::check()) {
            return redirect('/');
        }
        return view('front.auth');
    }

    // Memproses Login
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // intended() akan membawa user kembali ke halaman yang mereka tuju sebelum dicegat login (misal: checkout)
            return redirect()->intended('/'); 
        }

        // Jika gagal, kembali ke form dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang kamu masukkan salah.',
        ])->withInput($request->only('email'));
    }

    // Memproses Keluar/Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}