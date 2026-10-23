<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - MAS-IT VMS</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=5">
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-scroll::-webkit-scrollbar { height: 4px; }
        .nav-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">

@php
    $roleId = Auth::user()->id_role ?? 0;
    $currentRoute = Route::currentRouteName();
    $dashboardRoute = match($roleId) {
        1 => 'kepala.dashboard',
        2 => 'pimpinan.dashboard',
        3 => 'engineer.dashboard',
        default => 'login',
    };
    $dashboardUrl = route($dashboardRoute);
    $isMaster = str_contains($currentRoute, 'master.');
    $navActive = 'bg-blue-50 text-[#003399] font-semibold';
    $navIdle = 'text-slate-500 hover:text-[#003399] hover:bg-slate-100 font-medium';
@endphp

<!-- ================= TOP NAVBAR ================= -->
<header class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-[0_1px_3px_rgba(0,0,0,0.04)]">
    <div class="mx-auto max-w-[1200px] px-4">
        <div class="flex items-center justify-between h-16 gap-2">
            <!-- Logo -->
            <a href="{{ $dashboardUrl }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/logo-masit.png') }}" alt="MAS-IT" class="h-12 w-auto object-contain">
            </a>

            <!-- Menu desktop -->
            <nav class="hidden lg:flex items-center gap-1 text-[13px]">
                <a href="{{ $dashboardUrl }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg whitespace-nowrap transition {{ str_contains($currentRoute, 'dashboard') ? $navActive : $navIdle }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('kunjungan.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg whitespace-nowrap transition {{ str_contains($currentRoute, 'kunjungan') ? $navActive : $navIdle }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kunjungan
                </a>
                @if($roleId == 3)
                <a href="{{ route('peminjaman.pengembalian') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg whitespace-nowrap transition {{ str_contains($currentRoute, 'peminjaman') ? $navActive : $navIdle }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1m18 0v-1a3 3 0 00-3-3h-1m-4 8h6m-6 0H6m12 0a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-4h.01M6 20h.01"/></svg>
                    Tools Saya
                </a>
                @endif
                @if($roleId == 1 || $roleId == 2)
                <div class="relative" id="masterDropdownWrap">
                    <button onclick="toggleMasterDropdown(event)" class="flex items-center gap-1.5 px-3 py-2 rounded-lg whitespace-nowrap transition {{ $isMaster ? $navActive : $navIdle }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                        Master Data
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="masterDropdown" class="hidden absolute left-0 mt-2 w-52 bg-white rounded-xl border border-slate-200 shadow-xl py-2 text-[13px]">
                        <a href="{{ route('master.pengguna.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 {{ str_contains($currentRoute, 'master.pengguna') ? 'text-[#003399] font-semibold' : 'text-slate-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Data Pengguna
                        </a>
                        <a href="{{ route('master.customer.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 {{ str_contains($currentRoute, 'master.customer') ? 'text-[#003399] font-semibold' : 'text-slate-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Data Customer
                        </a>
                        <a href="{{ route('master.site.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 {{ str_contains($currentRoute, 'master.site') ? 'text-[#003399] font-semibold' : 'text-slate-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Data Site
                        </a>
                        {{-- QA ronde 2 #1: manajemen akun terpusat — menu pimpinan & engineer dilebur ke Data Pengguna --}}
                        <a href="{{ route('master.tool.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 {{ str_contains($currentRoute, 'master.tool') ? 'text-[#003399] font-semibold' : 'text-slate-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                            Tools & Alat Kerja
                        </a>
                        <a href="{{ route('master.format-nomor.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 {{ str_contains($currentRoute, 'master.format-nomor') ? 'text-[#003399] font-semibold' : 'text-slate-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            Format Nomor
                        </a>
                        <a href="{{ route('master.opsi-formulir.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-slate-50 {{ str_contains($currentRoute, 'master.opsi-formulir') ? 'text-[#003399] font-semibold' : 'text-slate-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Opsi Formulir
                        </a>
                    </div>
                </div>
                @endif
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg whitespace-nowrap transition {{ str_contains($currentRoute, 'laporan') ? $navActive : $navIdle }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
            </nav>

            <!-- Kanan: profil (QA #2: ikon gear duplikat dibuang, cukup dropdown profil) -->
            <div class="flex items-center gap-1.5 shrink-0">
                <div class="relative" id="profileDropdownWrap">
                    <button onclick="toggleProfileDropdown(event)" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">
                        <span class="w-8 h-8 rounded-full bg-[#002266] text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->nama ?? 'U', 0, 1)) }}
                        </span>
                        <span class="hidden md:block text-[13px] font-semibold text-slate-700 max-w-[110px] truncate">{{ Auth::user()->nama }}</span>
                        <svg class="hidden md:block w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-200 shadow-xl py-2 text-[13px]">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="font-semibold text-slate-800 truncate">{{ Auth::user()->nama }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-600 hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-rose-600 hover:bg-rose-50 text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Hamburger (mobile) -->
                <button onclick="toggleMobileNav()" class="lg:hidden p-2 text-slate-500 hover:text-[#003399] hover:bg-slate-100 rounded-lg transition" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu mobile -->
    <div id="mobileNav" class="hidden lg:hidden border-t border-slate-200 bg-white">
        <nav class="px-4 py-3 space-y-1 text-sm max-h-[70vh] overflow-y-auto">
            <a href="{{ $dashboardUrl }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'dashboard') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('kunjungan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'kunjungan') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kunjungan
            </a>
            @if($roleId == 3)
            <a href="{{ route('peminjaman.pengembalian') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'peminjaman') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 15v-1a3 3 0 00-3-3H6a3 3 0 00-3 3v1m18 0v-1a3 3 0 00-3-3h-1m-4 8h6m-6 0H6m12 0a2 2 0 002-2v-4a2 2 0 00-2-2h-2m-4-4h.01M6 20h.01"/></svg>
                Tools Saya
            </a>
            @endif
            @if($roleId == 1 || $roleId == 2)
            <p class="px-3 pt-3 pb-1 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Master Data</p>
            <a href="{{ route('master.pengguna.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'master.pengguna') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Data Pengguna</a>
            <a href="{{ route('master.customer.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'master.customer') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Data Customer</a>
            <a href="{{ route('master.site.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'master.site') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Data Site</a>
            {{-- QA ronde 2 #1: menu pimpinan & engineer dilebur ke Data Pengguna --}}
            <a href="{{ route('master.tool.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'master.tool') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                Tools & Alat Kerja</a>
            <a href="{{ route('master.format-nomor.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'master.format-nomor') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                Format Nomor</a>
            <a href="{{ route('master.opsi-formulir.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'master.opsi-formulir') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                Opsi Formulir</a>
            @endif
            <div class="border-t border-slate-200 my-2 -mx-4"></div>
            <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ str_contains($currentRoute, 'laporan') ? $navActive : $navIdle }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
            <div class="border-t border-slate-200 my-2 -mx-4"></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-rose-600 hover:bg-rose-50 font-medium text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </nav>
    </div>
</header>

<!-- ================= KONTEN ================= -->
<div class="mx-auto w-full max-w-[1200px] px-4 py-6 md:py-8">
    <main class="min-h-[60vh]">
        @yield('content')
    </main>
    <p class="text-center text-[11px] text-slate-400 mt-10">&copy; {{ date('Y') }} MAS-IT VMS. Seluruh hak cipta dilindungi.</p>
</div>

<!-- Global Confirmation Pop-up -->
<div id="globalConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="w-full max-w-md p-6 bg-white rounded-2xl shadow-2xl text-center">
        <div class="mx-auto flex items-center justify-center w-12 h-12 rounded-full bg-blue-50 text-[#003399] mb-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 id="globalConfirmTitle" class="text-base font-bold text-slate-800 mb-2">Konfirmasi Tindakan</h3>
        <p id="globalConfirmMessage" class="text-xs text-slate-500 mb-5 leading-relaxed font-medium">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
        <div class="flex items-center justify-center gap-3">
            <button type="button" onclick="closeConfirmModal()" class="w-1/2 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Batal</button>
            <button type="button" id="globalConfirmSubmitBtn" class="w-1/2 py-2.5 px-4 bg-[#002266] hover:bg-[#001a4d] text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-900/20 transition">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
    function toggleMasterDropdown(e) {
        e.stopPropagation();
        document.getElementById('masterDropdown').classList.toggle('hidden');
        document.getElementById('profileDropdown').classList.add('hidden');
    }
    function toggleProfileDropdown(e) {
        e.stopPropagation();
        document.getElementById('profileDropdown').classList.toggle('hidden');
        const md = document.getElementById('masterDropdown');
        if (md) md.classList.add('hidden');
    }
    function toggleMobileNav() {
        document.getElementById('mobileNav').classList.toggle('hidden');
    }
    // Tekan ESC untuk menutup modal yang sedang terbuka (alternatif tombol X)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal"].fixed:not(.hidden)').forEach(function(m) {
                m.classList.add('hidden');
            });
            var gc = document.getElementById('globalConfirmModal');
            if (gc) gc.classList.add('hidden');
        }
    });
    document.addEventListener('click', function(e) {
        const md = document.getElementById('masterDropdown');
        const pd = document.getElementById('profileDropdown');
        if (md && !document.getElementById('masterDropdownWrap').contains(e.target)) md.classList.add('hidden');
        if (pd && !document.getElementById('profileDropdownWrap').contains(e.target)) pd.classList.add('hidden');
    });

    let targetFormToSubmit = null;
    function showConfirmModal(formElement, title, message) {
        targetFormToSubmit = formElement;
        document.getElementById('globalConfirmTitle').innerText = title || 'Konfirmasi Tindakan';
        document.getElementById('globalConfirmMessage').innerText = message || 'Lanjutkan proses ini?';
        document.getElementById('globalConfirmModal').classList.remove('hidden');
    }
    function closeConfirmModal() {
        document.getElementById('globalConfirmModal').classList.add('hidden');
        targetFormToSubmit = null;
    }
    document.getElementById('globalConfirmSubmitBtn').addEventListener('click', () => {
        if (targetFormToSubmit) targetFormToSubmit.submit();
    });
</script>
@stack('scripts')
</body>
</html>
