<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek sisa hari ganti username
        $bisaGantiUsername = true;
        $sisaHari = 0;
        
        if ($user->username_changed_at) {
            $batasWaktu = $user->username_changed_at->addDays(7);
            if (now()->lessThan($batasWaktu)) {
                $bisaGantiUsername = false;
                $sisaHari = now()->diffInDays($batasWaktu) + 1; // Pembulatan hari
            }
        }

        return view('profile.index', compact('user', 'bisaGantiUsername', 'sisaHari'));
    }

    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id_pengguna);

        // Validasi input
        $request->validate([
            'email' => 'required|email|unique:pengguna,email,' . $user->id_pengguna . ',id_pengguna',
            'kontak' => 'required|string|max:20',
            'password_lama' => 'required|string',
            'password_baru' => 'nullable|string|min:6|confirmed',
        ]);

        // Verifikasi password lama sebelum izinkan perubahan
        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->with('error', 'Gagal menyimpan! Password Lama yang Anda masukkan salah.');
        }

        // Logic Ganti Username (Jika berubah)
        if ($request->filled('username') && $request->username !== $user->username) {
            // Cek apakah username sudah dipakai orang lain
            $cekUsername = User::where('username', $request->username)->where('id_pengguna', '!=', $user->id_pengguna)->first();
            if($cekUsername) {
                return back()->with('error', 'Username sudah digunakan oleh orang lain.');
            }

            // Cek apakah sudah lewat 7 hari
            if ($user->username_changed_at) {
                $batasWaktu = $user->username_changed_at->copy()->addDays(7);
                if (now()->lessThan($batasWaktu)) {
                    return back()->with('error', 'Gagal! Username baru bisa diganti lagi setelah ' . now()->diffInDays($batasWaktu) . ' hari.');
                }
            }

            $user->username = $request->username;
            $user->username_changed_at = now();
        }

        // Update Data Umum
        $user->email = $request->email;
        $user->kontak = $request->kontak;

        // Update Password (Jika diisi)
        if ($request->filled('password_baru')) {
            $user->password = Hash::make($request->password_baru);
        }

        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}