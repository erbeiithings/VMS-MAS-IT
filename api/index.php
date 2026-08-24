<?php

// 1. Bikin folder bayangan biar sistem Vercel nggak ngambek (bisa nulis cache)
$dirs = [
    '/tmp/storage/logs',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/bootstrap/cache'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Paksa sesi pakai cookie
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_DRIVER=array');

// 3. INI OBAT UTAMANYA: Tricking Laravel biar ngira dia dapet request HTTPS murni
$_SERVER['HTTPS'] = 'on';
$_SERVER['IS_VERCEL'] = true;

// 4. Lanjut jalanin webnya
require __DIR__ . '/../public/index.php';