<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Cek login via username atau email
        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$fieldType => $request->username, 'password' => $request->password], $request->filled('remember'))) {
            $user = Auth::user();

            // Cek status keaktifan akun
            if ($user->status_akun !== 'Aktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['username' => 'Akun Anda dinonaktifkan. Hubungi Pimpinan.']);
            }

            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'username' => 'Username/Email atau Password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil keluar dari sistem.');
    }

    protected function redirectBasedOnRole($user)
    {
        switch ($user->id_role) {
            case 1: // Kepala Pimpinan
                return redirect()->route('kepala.dashboard');
            case 2: // Pimpinan
                return redirect()->route('pimpinan.dashboard');
            case 3: // Engineer
                return redirect()->route('engineer.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['username' => 'Role tidak valid.']);
        }
    }
}