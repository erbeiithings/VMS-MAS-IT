<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Visit Management System</title>
    <!-- Tailwind CSS CDN & Font Poppins -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<!-- Background Body diubah jadi Full Gradasi Biru MAS-IT -->
<body class="min-h-screen bg-gradient-to-br from-[#001233] via-[#002266] to-[#0044cc] flex items-center justify-center p-4 text-slate-800">

    <!-- Kotak Login (Card) -->
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] relative z-10">
        
        <!-- Header / Branding MAS-IT -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo-masit.png') }}" alt="MAS-IT Logo" class="h-16 mx-auto mb-3 object-contain">
            <h1 class="text-2xl font-bold tracking-tight text-[#002266]">MAS-IT</h1>
            <p class="text-[10px] text-slate-500 mt-1 uppercase tracking-widest font-semibold">Visit Management System</p>
        </div>

        <!-- Alert Notifikasi / Error -->
        @if(session('success'))
            <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2 font-medium">
                <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-xs font-semibold text-slate-700 mb-1.5">Username / Email</label>
                <div class="relative">
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-transparent transition-all"
                           placeholder="Masukkan username atau email">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-[#003399] focus:border-transparent transition-all"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs font-medium">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-[#002266]">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#003399] focus:ring-[#003399]">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3 px-4 bg-[#002266] hover:bg-[#001233] text-white font-semibold text-sm rounded-xl shadow-lg shadow-blue-900/30 active:scale-[0.98] transition duration-200">
                Masuk ke Sistem
            </button>
        </form>
    </div>
</body>
</html>