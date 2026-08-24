<?php

namespace App\Http\Controllers;

use App\Models\Engineer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EngineerController extends Controller
{
    public function index()
    {
        $engineers = Engineer::with('user')->latest()->paginate(10);
        return view('master.engineer.index', compact('engineers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:pengguna,username',
            'email' => 'required|email|max:100|unique:pengguna,email',
            'password' => 'required|string|min:6',
            'kontak' => 'required|string|max:20',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'kontak' => $request->kontak,
            'id_role' => 3, // Engineer
            'status_akun' => 'Aktif',
            'dibuat_oleh' => Auth::user()->id_pengguna,
        ]);

        Engineer::create([
            'id_pengguna' => $user->id_pengguna,
            'kontak' => $request->kontak,
            'status_ketersediaan' => 'Tersedia',
        ]);

        return redirect()->back()->with('success', 'Data Engineer & Akun Login berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $engineer = Engineer::findOrFail($id);
        $user = $engineer->user;

        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:pengguna,username,' . $user->id_pengguna . ',id_pengguna',
            'email' => 'required|email|max:100|unique:pengguna,email,' . $user->id_pengguna . ',id_pengguna',
            'kontak' => 'required|string|max:20',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $userData = [
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'kontak' => $request->kontak,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $engineer->update([
            'kontak' => $request->kontak,
            'status_ketersediaan' => $request->status_ketersediaan,
        ]);

        return redirect()->back()->with('success', 'Data Engineer berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $engineer = Engineer::findOrFail($id);
        if ($engineer->user) {
            $engineer->user->delete(); // On delete cascade akan menghapus data engineer
        } else {
            $engineer->delete();
        }

        return redirect()->back()->with('success', 'Engineer berhasil dihapus!');
    }
}