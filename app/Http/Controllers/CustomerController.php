<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('master.customer.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'pic' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100',
        ]);

        Customer::create($validated);

        return redirect()->back()->with('success', 'Customer berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'pic' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100',
        ]);

        $customer->update($validated);

        return redirect()->back()->with('success', 'Data Customer berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->back()->with('success', 'Customer berhasil dihapus!');
    }
}