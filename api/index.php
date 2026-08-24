<?php

// 1. Buat struktur folder bayangan di folder memori sementara (/tmp) milik Vercel
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

// 2. Beri penanda rahasia kalau kita sedang berjalan di Vercel
$_SERVER['IS_VERCEL'] = true;

putenv('SESSION_DRIVER=cookie');
putenv('SESSION_DOMAIN=' . $_SERVER['HTTP_HOST']); // Otomatis ngebaca domain vercel lu
putenv('SESSION_SECURE_COOKIE=true');

// 3. Lanjutkan ke sistem utama Laravel
require __DIR__ . '/../public/index.php';