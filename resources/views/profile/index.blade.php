@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header_title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold shadow-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-[#001233] to-[#0044cc] p-8 flex items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center text-[#002266] text-4xl font-bold shadow-lg border-4 border-blue-200/30">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>
            <div class="text-white">
                <h2 class="text-2xl font-bold">{{ $user->nama }}</h2>
                <p class="text-blue-200 text-sm font-medium mt-1">{{ $user->role->nama_role ?? 'Pengguna' }} | {{ $user->email }}</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-6 md:p-8 space-y-8">
            @csrf
            @method('PUT')

            <!-- Data Akun -->
            <div>
                <h3 class="text-base font-bold text-[#002266] mb-4 border-b border-slate-100 pb-2">Informasi Akun</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-2">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" 
                               {{ !$bisaGantiUsername ? 'readonly' : '' }}
                               class="w-full px-4 py-2.5 bg-slate-50 border {{ !$bisaGantiUsername ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-slate-300 text-slate-800 focus:ring-[#003399]' }} rounded-xl text-sm font-medium outline-none focus:ring-2">
                        @if(!$bisaGantiUsername)
                            <p class="text-[10px] text-amber-600 font-bold mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Anda baru bisa mengubah username lagi dalam {{ $sisaHari }} hari.
                            </p>
                        @else
                            <p class="text-[10px] text-slate-400 mt-1.5">Username hanya dapat diubah 1 kali dalam 7 hari.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[#003399]">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-2">No. HP / Kontak (WA)</label>
                        <input type="text" name="kontak" value="{{ old('kontak', $user->kontak) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[#003399]">
                    </div>
                </div>
            </div>

            <!-- Ganti Password -->
            <div>
                <h3 class="text-base font-bold text-[#002266] mb-4 border-b border-slate-100 pb-2">Ubah Password (Opsional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-2">Password Baru</label>
                        <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin ganti" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[#003399]">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-bold mb-2">Ulangi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-[#003399]">
                    </div>
                </div>
            </div>

            <!-- Validasi Password Lama & Submit -->
            <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl mt-8">
                <h4 class="text-xs font-bold text-amber-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Verifikasi Keamanan
                </h4>
                <div class="flex flex-col md:flex-row items-end gap-4">
                    <div class="w-full">
                        <label class="block text-amber-900 text-[11px] font-semibold mb-1.5">Masukkan Password Lama Anda untuk memvalidasi perubahan ini:</label>
                        <input type="password" name="password_lama" required placeholder="Password saat ini" class="w-full px-4 py-2.5 bg-white border border-amber-300 text-slate-800 rounded-xl text-sm font-medium outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <button type="submit" class="w-full md:w-auto whitespace-nowrap px-6 py-2.5 bg-[#002266] hover:bg-[#001233] text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 transition h-[42px]">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection