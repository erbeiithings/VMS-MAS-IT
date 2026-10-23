<?php

namespace App\Http\Controllers;

use App\Models\FormOption;
use Illuminate\Http\Request;

class OpsiFormulirController extends Controller
{
    /**
     * QA #15: kelola daftar pilihan formulir (jenis biaya & kategori foto)
     * yang dipakai di halaman kunjungan. Admin bisa tambah/hapus opsi;
     * opsi "Lainnya" selalu tersedia otomatis di formulir.
     */
    public function index()
    {
        $jenisBiaya = FormOption::where('grup', 'jenis_biaya')->orderBy('nama')->get();
        $kategoriFoto = FormOption::where('grup', 'kategori_foto')->orderBy('nama')->get();
        return view('master.opsi_formulir.index', compact('jenisBiaya', 'kategoriFoto'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grup' => 'required|in:jenis_biaya,kategori_foto',
            'nama' => 'required|string|max:100',
        ]);

        $nama = trim($request->nama);
        if (strcasecmp($nama, 'Lainnya') === 0) {
            return redirect()->back()->with('error', 'Opsi "Lainnya" sudah tersedia otomatis di formulir.');
        }

        FormOption::firstOrCreate(['grup' => $request->grup, 'nama' => $nama]);

        return redirect()->back()->with('success', 'Opsi "' . $nama . '" berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $opsi = FormOption::findOrFail($id);
        $nama = $opsi->nama;
        $opsi->delete();

        return redirect()->back()->with('success', 'Opsi "' . $nama . '" berhasil dihapus!');
    }
}
