<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $query = Tool::query();

        // Fitur Pencarian (Search by Nama Alat, Kode, atau Kategori)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_alat', 'like', '%' . $search . '%')
                  ->orWhere('kode', 'like', '%' . $search . '%')
                  ->orWhere('kategori', 'like', '%' . $search . '%');
        }

        // Fitur Sortir
        $sort = $request->get('sort', 'terbaru');
        if ($sort == 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $tools = $query->paginate(10)->appends($request->all());
        
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