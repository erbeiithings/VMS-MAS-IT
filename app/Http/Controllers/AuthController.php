<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Nampilin form login
    public function showLogin()
    {
        // Kalau user iseng buka halaman login padahal udah masuk, langsung cegat dan arahin ke dashboardnya
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    // Proses pas tombol "Masuk" diklik
    public function login(Request $request)
    {
        // Pastiin username dan password diisi
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cek login via username atau email (Cerdas nih, bisa deteksi format email atau string biasa)
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Coba cocokin data login ke database. Kalau bener, masuk blok if ini
        if (Auth::attempt([$fieldType => $request->username, 'password' => $request->password], $request->filled('remember'))) {
            $user = Auth::user();

            // Cek status keaktifan akun. Kalau akunnya diblokir, langsung paksa logout saat itu juga
            if ($user->status_akun !== 'Aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['username' => 'Akun Anda dinonaktifkan. Hubungi Pimpinan.']);
            }

            // Kalau aman, regenerate session (buat hindarin security issue/session fixation) terus arahin sesuai role
            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        // Kalau password atau username salah, kembalikan ke form dan kasih error
        return back()->withErrors([
            'username' => 'Username/Email atau Password salah.',
        ])->onlyInput('username');
    }

    // Proses keluar dari sistem
    public function logout(Request $request)
    {
        Auth::logout(); // Hancurin kredensial
        
        // Bersihin total session dan token biar gak ada sisa jejak login
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Berhasil keluar dari sistem.');
    }

    // Fungsi "tukang parkir" buat nge-redirect user ke dashboard sesuai role mereka
    protected function redirectBasedOnRole($user)
    {
        switch ($user->id_role) {
            case 1: // Role ID 1 buat Kepala Pimpinan
                return redirect()->route('kepala.dashboard');
            case 2: // Role ID 2 buat Pimpinan biasa
                return redirect()->route('pimpinan.dashboard');
            case 3: // Role ID 3 buat orang lapangan (Engineer)
                return redirect()->route('engineer.dashboard');
            default:
                // Jaga-jaga kalau role-nya ngaco / ga terdaftar, paksa logout aja
                Auth::logout();
                return redirect()->route('login')->withErrors(['username' => 'Role tidak valid.']);
        }
    }
}