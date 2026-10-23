<?php

namespace App\Support;

/**
 * QA ronde 2: resolver URL & path file upload (dokumentasi & bukti nota).
 *
 * Upload baru tersimpan di webroot (di production: public_html, sejajar folder
 * aplikasi). File LAMA yang terlanjur tersimpan di public/ aplikasi tidak
 * terjangkau webroot, jadi ditampilkan lewat route streaming 'uploads.lokal'
 * agar foto lama tampil lagi tanpa memindahkan file secara manual.
 */
class Uploads
{
    /** Folder webroot efektif (public_html di production bila ada). */
    public static function webroot(): string
    {
        $sibling = dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html';
        return is_dir($sibling) ? $sibling : public_path();
    }

    /** Path disk file upload, atau null bila tidak ditemukan di kedua lokasi. */
    public static function filePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        $rel = str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'));
        $diWebroot = self::webroot() . DIRECTORY_SEPARATOR . $rel;
        if (is_file($diWebroot)) {
            return $diWebroot;
        }
        $diApp = public_path($rel);
        if (is_file($diApp)) {
            return $diApp;
        }
        return null;
    }

    /** URL untuk menampilkan file upload di browser. */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        $rel = ltrim($path, '/');
        $diWebroot = self::webroot() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        if (is_file($diWebroot)) {
            return asset($rel);
        }
        if (is_file(public_path(str_replace('/', DIRECTORY_SEPARATOR, $rel)))) {
            return route('uploads.lokal', ['path' => $rel]);
        }
        return asset($rel);
    }
}
