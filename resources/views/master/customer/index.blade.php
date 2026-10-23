@extends('layouts.app')

@section('title', 'Data Customer')
@section('header_title', 'Manajemen Customer')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </span>
            <div>
                <h1 class="text-lg font-bold text-slate-900">Daftar Customer</h1>
                <p class="text-xs text-slate-500 mt-0.5">Kelola informasi perusahaan customer mitra MAS-IT</p>
            </div>
        </div>
        <button onclick="document.getElementById('modalTambahCustomer').classList.remove('hidden')"
                class="bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg px-4 py-2.5 transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Tambah Customer
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('master.customer.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pencarian Data</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari Nama Perusahaan, PIC, atau Email..." class="w-full h-11 pl-10 pr-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label for="sort" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan</label>
                <select id="sort" name="sort" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru (Terbaru)</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Paling Lama (Terlama)</option>
                </select>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="h-11 px-5 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition flex items-center justify-center">
                    Terapkan
                </button>
                @if(request()->has('search') || request()->has('sort'))
                    <a href="{{ route('master.customer.index') }}" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-semibold rounded-lg transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Perusahaan</th>
                        <th class="px-4 py-3 text-left font-semibold">PIC</th>
                        <th class="px-4 py-3 text-left font-semibold">Kontak / Email</th>
                        <th class="px-4 py-3 text-left font-semibold">Alamat & Koordinat</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 border-t border-slate-100">
                                @if($c->kode)
                                <a href="{{ route('master.customer.show', $c->kode) }}" class="font-semibold text-[#003399] hover:underline" title="Lihat detail customer">{{ $c->nama_perusahaan }}</a>
                                @else
                                <span class="font-semibold text-slate-800">{{ $c->nama_perusahaan }}</span>
                                @endif
                                @if($c->kode)<div class="text-[10px] font-mono font-semibold text-slate-400 mt-0.5"><a href="{{ route('master.customer.show', $c->kode) }}" class="hover:text-[#003399] hover:underline">{{ $c->kode }}</a></div>@endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 font-semibold text-slate-800">{{ $c->pic }}</td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="text-slate-700">{{ $c->telepon }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $c->email }}</p>
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100">
                                <p class="text-slate-700 max-w-xs truncate">{{ $c->alamat }}</p>
                                @if($c->latitude && $c->longitude)
                                    <p class="text-[11px] font-semibold text-emerald-600 mt-1">GPS: {{ $c->latitude }}, {{ $c->longitude }}
                                @else
                                    <p class="text-[11px] font-semibold text-rose-500 mt-1">GPS: Belum diatur</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 border-t border-slate-100 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button onclick="openCabangModal('{{ $c->kode }}', '{{ addslashes($c->nama_perusahaan) }}')"
                                            class="p-2 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Kelola Cabang/Site">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </button>
                                    <button onclick="openEditModal({{ json_encode($c) }})" class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('master.customer.destroy', $c->kode) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Customer', 'Apakah Anda yakin ingin menghapus customer {{ $c->nama_perusahaan }}?')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 border-t border-slate-100 text-center text-slate-400 font-medium italic">Data customer tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $customers->links() }}
        </div>
    </div>

</div>

<!-- Modal Tambah -->
<div id="modalTambahCustomer" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Tambah Customer Baru</h4>
            <button onclick="document.getElementById('modalTambahCustomer').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('master.customer.store') }}" method="POST" class="space-y-4 text-sm">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Person in Charge (PIC)</label>
                    <input type="text" name="pic" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="tel" name="telepon" required inputmode="tel" pattern="\+?[0-9\s\-().]+" title="Hanya angka (boleh diawali +, spasi, strip, titik, kurung)" placeholder="cth: 081234567890" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email Perusahaan / PIC</label>
                <input type="email" name="email" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat Kantor</label>
                <textarea name="alamat" rows="2" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Titik Koordinat (Bisa Paste dari Google Maps)</label>
                <div class="flex gap-2">
                    <input type="text" id="add_latitude" name="latitude" placeholder="Contoh: -6.1206733" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <input type="text" id="add_longitude" name="longitude" placeholder="Contoh: 106.880495" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <button type="button" onclick="getLocation('add_latitude', 'add_longitude')" class="h-11 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold whitespace-nowrap">
                        📍 Auto GPS
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahCustomer').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEditCustomer" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-lg font-bold text-slate-900">Edit Customer</h4>
            <button onclick="document.getElementById('modalEditCustomer').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>
        <form id="formEditCustomer" method="POST" class="space-y-4 text-sm">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Perusahaan</label>
                <input type="text" id="edit_nama_perusahaan" name="nama_perusahaan" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">PIC</label>
                    <input type="text" id="edit_pic" name="pic" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Telepon</label>
                    <input type="tel" id="edit_telepon" name="telepon" required inputmode="tel" pattern="\+?[0-9\s\-().]+" title="Hanya angka (boleh diawali +, spasi, strip, titik, kurung)" placeholder="cth: 081234567890" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                <input type="email" id="edit_email" name="email" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                <textarea id="edit_alamat" name="alamat" rows="2" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Titik Koordinat (Bisa Paste dari Google Maps)</label>
                <div class="flex gap-2">
                    <input type="text" id="edit_latitude" name="latitude" placeholder="Contoh: -6.1206733" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <input type="text" id="edit_longitude" name="longitude" placeholder="Contoh: 106.880495" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-base text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    <button type="button" onclick="getLocation('edit_latitude', 'edit_longitude')" class="h-11 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold whitespace-nowrap">
                        📍 Auto GPS
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalEditCustomer').classList.add('hidden')" class="h-11 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[13px] font-semibold rounded-lg transition">Batal</button>
                <button type="submit" class="h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola Cabang -->
<div id="modalKelolaCabang" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-4xl p-6 shadow-2xl flex flex-col max-h-[90vh]">
        <div class="flex justify-between items-center mb-5">
            <h4 class="text-base font-bold text-slate-900">Kelola Cabang: <span id="namaPerusahaanCabang" class="text-[#003399]"></span></h4>
            <button onclick="document.getElementById('modalKelolaCabang').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition text-2xl leading-none">&times;</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 overflow-y-auto pr-1">
            <div class="md:col-span-1 bg-slate-50 p-4 rounded-xl border border-slate-200 h-fit">
                <h5 id="judulFormCabang" class="font-bold text-slate-700 text-xs mb-3 uppercase tracking-wider">Tambah Cabang</h5>
                <form id="formTambahCabang" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <input type="hidden" name="_method" id="methodCabang" value="" disabled>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Cabang</label>
                        <input type="text" id="cabang_nama" name="nama_cabang" required class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Lengkap</label>
                        <textarea id="cabang_alamat" name="alamat_lengkap" rows="2" required class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Titik GPS (Optional)</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" id="site_latitude" name="latitude" placeholder="Latitude" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                            <input type="text" id="site_longitude" name="longitude" placeholder="Longitude" class="w-full h-11 px-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                        </div>
                        <button type="button" onclick="getLocation('site_latitude', 'site_longitude')" class="w-full h-11 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition text-xs font-semibold">
                            📍 Auto GPS
                        </button>
                    </div>
                    <button type="submit" id="btnSimpanCabang" class="w-full h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition mt-1">Simpan Cabang</button>
                    <button type="button" id="btnBatalEditCabang" onclick="resetFormCabang()" class="hidden w-full h-11 px-4 bg-white border border-slate-300 hover:bg-slate-50 text-slate-600 text-[13px] font-semibold rounded-lg transition mt-1">Batal Edit</button>
                </form>
            </div>

            <div class="md:col-span-2">
                <h5 class="font-bold text-slate-700 text-xs mb-3 uppercase tracking-wider">Daftar Cabang Aktif</h5>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="text-[11px] uppercase tracking-wider text-slate-500 bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Nama Cabang</th>
                                <th class="px-4 py-3 text-left font-semibold">Detail Lokasi</th>
                                <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_cabang">
                            <tr><td colspan="3" class="px-4 py-4 border-t border-slate-100 text-center text-slate-400 italic">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info GPS -->
<div id="modalInfoGPS" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xs p-6 shadow-2xl text-center">
        <div id="modalInfoIcon" class="mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4"></div>
        <h3 id="modalInfoTitle" class="text-base font-bold text-slate-900 mb-2"></h3>
        <p id="modalInfoMessage" class="text-xs text-slate-600 mb-6 font-medium leading-relaxed"></p>
        <button type="button" onclick="document.getElementById('modalInfoGPS').classList.add('hidden')" class="w-full h-11 px-4 bg-[#002266] hover:bg-[#001a4d] text-white text-[13px] font-semibold rounded-lg transition">
            Tutup
        </button>
    </div>
</div>

<script>
    function showGPSModal(title, message, isSuccess) {
        document.getElementById('modalInfoTitle').innerText = title;
        document.getElementById('modalInfoMessage').innerText = message;

        const iconContainer = document.getElementById('modalInfoIcon');
        if(isSuccess) {
            iconContainer.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 bg-emerald-100 text-emerald-600 border border-emerald-200';
            iconContainer.innerHTML = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else {
            iconContainer.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-full mb-4 bg-rose-100 text-rose-600 border border-rose-200';
            iconContainer.innerHTML = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        }

        document.getElementById('modalInfoGPS').classList.remove('hidden');
    }

    function getLocation(latId, longId) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById(latId).value = position.coords.latitude;
                    document.getElementById(longId).value = position.coords.longitude;
                    showGPSModal('Lokasi Ditemukan!', 'Titik koordinat GPS Anda berhasil didapatkan dan dimasukkan ke dalam form.', true);
                },
                (error) => {
                    showGPSModal('Gagal Membaca GPS', 'Pastikan GPS aktif dan izin lokasi (Location) pada browser Anda telah diizinkan.', false);
                },
                { enableHighAccuracy: true }
            );
        } else {
            showGPSModal('Tidak Mendukung', 'Browser atau perangkat Anda tidak mendukung fitur Geolocation.', false);
        }
    }

    function openEditModal(customer) {
        document.getElementById('formEditCustomer').action = `/master/customer/${customer.kode}`;
        document.getElementById('edit_nama_perusahaan').value = customer.nama_perusahaan;
        document.getElementById('edit_pic').value = customer.pic;
        document.getElementById('edit_telepon').value = customer.telepon;
        document.getElementById('edit_email').value = customer.email;
        document.getElementById('edit_alamat').value = customer.alamat;

        document.getElementById('edit_latitude').value = customer.latitude || '';
        document.getElementById('edit_longitude').value = customer.longitude || '';

        document.getElementById('modalEditCustomer').classList.remove('hidden');
    }

    let kodeCustomerAktif = null;

    function resetFormCabang() {
        const form = document.getElementById('formTambahCabang');
        form.reset();
        form.action = `/master/customer/${kodeCustomerAktif}/site`;
        const method = document.getElementById('methodCabang');
        method.value = ''; method.disabled = true;
        document.getElementById('judulFormCabang').innerText = 'Tambah Cabang';
        document.getElementById('btnSimpanCabang').innerText = 'Simpan Cabang';
        document.getElementById('btnBatalEditCabang').classList.add('hidden');
    }

    // QA ronde 2 #4: muat data cabang ke form -> mode Edit Cabang
    function editCabang(idSite) {
        const site = (window._sitesCabang || {})[idSite];
        if (!site) return;
        document.getElementById('cabang_nama').value = site.nama_cabang || '';
        document.getElementById('cabang_alamat').value = site.alamat_lengkap || '';
        document.getElementById('site_latitude').value = site.latitude || '';
        document.getElementById('site_longitude').value = site.longitude || '';
        const form = document.getElementById('formTambahCabang');
        form.action = `/master/customer/site/${idSite}`;
        const method = document.getElementById('methodCabang');
        method.value = 'PUT'; method.disabled = false;
        document.getElementById('judulFormCabang').innerText = 'Edit Cabang';
        document.getElementById('btnSimpanCabang').innerText = 'Simpan Perubahan';
        document.getElementById('btnBatalEditCabang').classList.remove('hidden');
    }

    function openCabangModal(kodeCustomer, namaPerusahaan) {
        kodeCustomerAktif = kodeCustomer;
        window._sitesCabang = {};
        document.getElementById('namaPerusahaanCabang').innerText = namaPerusahaan;
        document.getElementById('formTambahCabang').action = `/master/customer/${kodeCustomer}/site`;
        resetFormCabang();
        document.getElementById('modalKelolaCabang').classList.remove('hidden');

        const tbody = document.getElementById('tbody_cabang');
        tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-4 border-t border-slate-100 text-center text-slate-400 italic">Memuat data cabang...</td></tr>';

        fetch(`/kunjungan/get-sites/${kodeCustomer}`)
            .then(res => res.json())
            .then(data => {
                tbody.innerHTML = '';
                if(data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-6 border-t border-slate-100 text-center text-slate-400 font-medium italic">Belum ada data cabang / site.</td></tr>';
                } else {
                    data.forEach(site => {
                        window._sitesCabang[site.id_site] = site;
                        let gpsText = (site.latitude && site.longitude)
                            ? `<p class="text-[11px] font-semibold text-emerald-600 mt-1">GPS: ${site.latitude}, ${site.longitude}</p>`
                            : `<p class="text-[11px] font-semibold text-rose-500 mt-1">GPS: Belum diatur</p>`;

                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 border-t border-slate-100 font-semibold text-[#003399]">${site.nama_cabang}</td>
                                <td class="px-4 py-3 border-t border-slate-100">
                                    <p class="text-slate-700">${site.alamat_lengkap}</p>
                                    ${gpsText}
                                </td>
                                <td class="px-4 py-3 border-t border-slate-100 text-center">
                                    <button type="button" onclick="editCabang(${site.id_site})"
                                            class="p-2 rounded-lg text-slate-400 hover:text-[#003399] hover:bg-blue-50 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="/master/customer/site/${site.id_site}" method="POST" class="inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" onclick="showConfirmModal(this.form, 'Hapus Cabang', 'Yakin menghapus cabang ${site.nama_cabang}?')"
                                                class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-4 border-t border-slate-100 text-center text-rose-500 font-semibold">Gagal menarik data cabang.</td></tr>';
            });
    }
</script>

<script>
    // No. Telepon: otomatis buang karakter selain angka, +, spasi, strip, titik, kurung
    document.querySelectorAll('input[name="telepon"]').forEach(el => {
        el.addEventListener('input', () => {
            const pos = el.selectionStart;
            const bersih = el.value.replace(/[^0-9+\s\-().]/g, '');
            if (bersih !== el.value) {
                el.value = bersih;
                try { el.setSelectionRange(pos - 1, pos - 1); } catch (e) {}
            }
        });
    });
</script>
@endsection
