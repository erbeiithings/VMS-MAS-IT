@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header_title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-[#003399] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
        <div>
            <h1 class="text-lg font-bold text-slate-900">Pengaturan Akun</h1>
            <p class="text-xs text-slate-500">Kelola informasi akun dan keamanan Anda</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <!-- Header Section -->
        <div class="p-6 flex items-center gap-5 border-b border-slate-200">
            <div class="w-16 h-16 rounded-full bg-[#002266] flex items-center justify-center text-white text-2xl font-bold shrink-0">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $user->nama }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->role->nama_role ?? 'Pengguna' }} &middot; {{ $user->email }}</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Data Akun -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-200 pb-2">Informasi Akun</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                               {{ !$bisaGantiUsername ? 'readonly' : '' }}
                               class="w-full h-11 px-4 bg-white border {{ !$bisaGantiUsername ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-slate-200 text-slate-800 focus:ring-[#003399] focus:border-[#003399]' }} rounded-lg text-sm outline-none focus:ring-2">
                        @if(!$bisaGantiUsername)
                            <p class="text-[11px] text-amber-600 font-semibold mt-1.5 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Anda baru bisa mengubah username lagi dalam {{ $sisaHari }} hari.
                            </p>
                        @else
                            <p class="text-[11px] text-slate-400 mt-1.5">Username hanya dapat diubah 1 kali dalam 7 hari.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full h-11 px-4 bg-white border border-slate-200 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1.5">No. HP / Kontak (WA)</label>
                        <input type="tel" name="kontak" inputmode="tel" pattern="\+?[0-9\s\-().]+" title="Hanya angka (boleh diawali +, spasi, strip, titik, kurung)" placeholder="cth: 081234567890" value="{{ old('kontak', $user->kontak) }}" required class="w-full h-11 px-4 bg-white border border-slate-200 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                </div>
            </div>

            <!-- Ganti Password -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-200 pb-2">Ubah Password (Opsional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1.5">Password Baru</label>
                        <input type="password" name="password_baru" placeholder="Kosongkan jika tidak ingin ganti" class="w-full h-11 px-4 bg-white border border-slate-200 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                    <div>
                        <label class="block text-slate-700 text-xs font-semibold mb-1.5">Ulangi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" placeholder="Ulangi password baru" class="w-full h-11 px-4 bg-white border border-slate-200 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#003399] focus:border-[#003399]">
                    </div>
                </div>
            </div>

            <!-- Validasi Password Lama & Submit -->
            <div class="bg-amber-50 border border-amber-200 p-5 rounded-xl">
                <h4 class="text-xs font-bold text-amber-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Verifikasi Keamanan
                </h4>
                <div class="flex flex-col md:flex-row items-end gap-4">
                    <div class="w-full">
                        <label class="block text-amber-900 text-[11px] font-semibold mb-1.5">Masukkan Password Lama Anda untuk memvalidasi perubahan ini:</label>
                        <input type="password" name="password_lama" required placeholder="Password saat ini" class="w-full h-11 px-4 bg-white border border-amber-300 text-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <button type="submit" class="w-full md:w-auto whitespace-nowrap px-6 py-2.5 h-11 bg-[#002266] hover:bg-[#001a4d] text-white rounded-lg text-[13px] font-semibold transition">
                        Simpan Perubahan Profil
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    // Kontak/No.WA: otomatis buang karakter selain angka, +, spasi, strip, titik, kurung
    document.querySelectorAll('input[name="kontak"]').forEach(el => {
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
