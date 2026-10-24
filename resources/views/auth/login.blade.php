<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MAS-IT VMS</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=5">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        input::-ms-reveal, input::-ms-clear { display: none; }
    </style>
</head>
<body class="bg-white font-sans text-slate-900">
<main class="min-h-screen bg-white">
    <!-- Garis aksen tipis di atas, sisanya dominan putih -->
    <div class="h-1 w-full bg-[#003399]"></div>
    <div class="mx-auto grid min-h-[calc(100vh-4px)] w-full max-w-[1200px] lg:grid-cols-2">

        <!-- ============ PANEL KIRI: BRANDING PUTIH (laptop saja) ============ -->
        <section class="relative hidden flex-col justify-between border-r border-slate-200 bg-white p-10 lg:flex">
            <div class="space-y-8">
                <a class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-masit.png') }}" alt="MAS-IT" class="h-10 w-10 rounded-xl border border-slate-200 bg-white object-contain p-1.5 shadow-sm">
                    <span class="text-lg font-bold tracking-tight text-slate-900">MAS-IT VMS</span>
                </a>
                <div class="space-y-6">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-[#003399]">Visit Management System</p>
                        <h1 class="mt-3 text-4xl font-bold leading-tight tracking-tight text-slate-900">Manajemen kunjungan<br/>engineer MAS-IT</h1>
                        <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-500">Satu tempat untuk menjadwalkan kunjungan customer, mencatat hasil pekerjaan, dan memantau laporan serta tools engineer.</p>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#003399]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                            </span>
                            <span class="text-sm font-medium text-slate-700">Catat hasil kunjungan langsung dari lapangan</span>
                        </li>
                        <li class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#003399]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                            </span>
                            <span class="text-sm font-medium text-slate-700">Persetujuan laporan atasan dengan jejak revisi</span>
                        </li>
                        <li class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#003399]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                            </span>
                            <span class="text-sm font-medium text-slate-700">Stok dan peminjaman tools terpantau otomatis</span>
                        </li>
                    </ul>
                </div>
            </div>
            <p class="text-sm text-slate-400">Aplikasi internal &mdash; gunakan akun kantor Anda</p>
        </section>

        <!-- ============ PANEL KANAN: FORM ============ -->
        <section class="flex flex-col justify-center bg-white px-5 py-10 sm:px-10">
            <div class="mx-auto w-full max-w-sm">
                <div class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <img src="{{ asset('images/logo-masit.png') }}" alt="MAS-IT" class="h-10 w-10 rounded-xl border border-slate-200 bg-white object-contain p-1.5 shadow-sm">
                    <span class="text-lg font-bold tracking-tight text-slate-900">MAS-IT VMS</span>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div>
                        <p class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#003399]">MAS-IT VMS</p>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">Masuk ke akun Anda</h2>
                        <p class="mt-2 text-sm text-slate-500">Kelola kunjungan, laporan, dan tools Anda.</p>
                    </div>

                    <div class="mt-7">
                        @if(session('success'))
                            <div class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs font-medium text-emerald-700">
                                <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-5 space-y-1.5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-medium text-rose-700">
                                @foreach($errors->all() as $error)
                                    <p class="flex items-center gap-2">
                                        <svg class="h-5 w-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        {{ $error }}
                                    </p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                            @csrf

                            <div class="space-y-1.5">
                                <label for="username" class="block text-sm font-semibold text-slate-700">Username / Email</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    </span>
                                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                                           class="h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-3 text-sm text-slate-900 placeholder-slate-400 transition focus:border-[#003399] focus:outline-none focus:ring-2 focus:ring-[#003399]/20"
                                           placeholder="Masukkan username atau email">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input type="password" id="password" name="password" required
                                           class="h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-11 text-sm text-slate-900 placeholder-slate-400 transition focus:border-[#003399] focus:outline-none focus:ring-2 focus:ring-[#003399]/20"
                                           placeholder="Masukkan password">
                                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition-colors hover:text-[#003399]" aria-label="Tampilkan password">
                                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>

                            <label class="flex cursor-pointer select-none items-center gap-2 text-xs font-medium text-slate-600 hover:text-[#002266]">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-[#003399] focus:ring-[#003399]">
                                <span>Ingat Saya</span>
                            </label>

                            <button type="submit"
                                    class="h-12 w-full rounded-xl bg-[#002266] px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-[#001a4d] hover:shadow-md active:scale-[0.98]">
                                Masuk
                            </button>
                        </form>

                        <p class="mt-7 text-center text-sm text-slate-500">Belum punya akun? Hubungi Kepala Pimpinan / Pimpinan.</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        eyeIcon.classList.toggle('text-[#003399]', type === 'text');
    });
</script>
</body>
</html>
