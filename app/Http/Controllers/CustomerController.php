<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Nampilin halaman utama daftar customer
    public function index(Request $request)
    {
        $query = Customer::query();

        // Fitur Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_perusahaan', 'like', '%' . $search . '%')
                  ->orWhere('pic', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        // Fitur Sortir
        $sort = $request->get('sort', 'terbaru');
        if ($sort == 'terlama') {
            $query->oldest('id_customer');
        } else {
            $query->latest('id_customer');
        }

        $customers = $query->paginate(10)->appends($request->all());
        
        return view('master.customer.index', compact('customers'));
    }

    // Buat nyimpen data customer baru ke database
    public function store(Request $request)
    {
        // Cek dulu inputannya udah bener belum, wajib diisi dan dibatesin panjang karakternya
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'pic' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'latitude' => 'nullable|string',   // Validasi Latitude (Boleh kosong)
            'longitude' => 'nullable|string',  // Validasi Longitude (Boleh kosong)
        ]);

        // Kalau lolos validasi, langsung insert ke tabel customers
        Customer::create($validated);

        // Balik ke halaman sebelumnya sekalian bawa pesan sukses
        return redirect()->back()->with('success', 'Customer berhasil ditambahkan!');
    }

    // Buat nyimpen hasil editan data customer
    public function update(Request $request, $id)
    {
        // Cari dulu customernya ada nggak berdasarkan ID, kalau nggak ada bakal otomatis error 404
        $customer = Customer::findOrFail($id);

        // Validasi lagi inputan editannya, aturannya sama kayak pas bikin baru
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'pic' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'latitude' => 'nullable|string',   // Validasi Latitude
            'longitude' => 'nullable|string',  // Validasi Longitude
        ]);

        // Timpa data lama dengan data baru yang udah tervalidasi
        $customer->update($validated);

        // Kasih notif sukses dan balik ke halaman list
        return redirect()->back()->with('success', 'Data Customer berhasil diperbarui!');
    }

    // Buat ngehapus data customer secara permanen
    public function destroy($id)
    {
        // Cari datanya dulu, baru di-delete dari database
        $customer = Customer::findOrFail($id);
        $customer->delete();

        // Refresh halaman bawa pesan sukses
        return redirect()->back()->with('success', 'Customer berhasil dihapus!');
    }
}