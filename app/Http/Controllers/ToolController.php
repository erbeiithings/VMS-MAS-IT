<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::latest()->paginate(10);
        return view('master.tool.index', compact('tools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:100',
            'kode' => 'required|string|max:50|unique:tools,kode',
            'kategori' => 'required|string|max:100',
            'spesifikasi' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia',
            'keterangan' => 'nullable|string',
        ]);

        Tool::create($validated);

        return redirect()->back()->with('success', 'Tool / Alat Kerja berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $tool = Tool::findOrFail($id);

        $validated = $request->validate([
            'nama_alat' => 'required|string|max:100',
            'kode' => 'required|string|max:50|unique:tools,kode,' . $tool->id_tool . ',id_tool',
            'kategori' => 'required|string|max:100',
            'spesifikasi' => 'nullable|string',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia',
            'keterangan' => 'nullable|string',
        ]);

        $tool->update($validated);

        return redirect()->back()->with('success', 'Data Tool berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tool = Tool::findOrFail($id);
        $tool->delete();

        return redirect()->back()->with('success', 'Tool berhasil dihapus!');
    }
}