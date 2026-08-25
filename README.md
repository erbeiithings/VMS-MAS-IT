# 🏢 MAS-IT VMS (Visit Management System)

**MAS-IT VMS** adalah sebuah Sistem Informasi berbasis web yang dirancang khusus untuk mengelola, mencatat, dan memonitor data kunjungan (*visit*) serta aktivitas pekerjaan lapangan. Sistem ini memudahkan perusahaan dalam melacak jadwal kunjungan Engineer, mengelola laporan hasil pekerjaan, serta memfasilitasi persetujuan dari tingkat Kepala hingga Pimpinan.

## ✨ Fitur Utama
*   **Multi-Role Access:** Sistem otentikasi aman dengan pembagian hak akses pengguna (Pimpinan, Kepala, dan Engineer).
*   **Manajemen Kunjungan:** Pencatatan dan penjadwalan aktivitas kunjungan Engineer ke lokasi klien/pekerjaan.
*   **Pelaporan Pekerjaan:** Pengisian hasil pekerjaan lapangan secara digital.
*   **Monitoring & Evaluasi:** Dasbor untuk memantau status kunjungan dan riwayat pekerjaan oleh atasan.
*   **User Management:** Pengelolaan data akun dan status keaktifan pengguna (Aktif/Nonaktif).

## 💻 Tech Stack
*   **Framework:** Laravel 10
*   **Bahasa Pemrograman:** PHP (Minimal v8.1)
*   **Database:** MySQL
*   **Server Lokal:** XAMPP / Laragon

---

## 🛠️ Persyaratan Sistem (Prerequisites)
Sebelum menjalankan *project* ini di laptop kamu, pastikan sudah menginstal perangkat lunak berikut:
1.  **XAMPP** (Pastikan menggunakan PHP minimal versi 8.1)
2.  **Composer** (Untuk mengelola *dependency* Laravel)
3.  **Git** (Untuk melakukan *clone repository*)

---

## 🚀 Cara Instalasi (Panduan untuk Tester)

Ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan aplikasi di komputer lokal (*Localhost*).

1. Clone Repository
Buka Terminal atau Command Prompt, arahkan ke folder tempat kamu ingin menyimpan *project* (misal: `C:\xampp\htdocs` atau folder `Documents`). Jalankan perintah:
```bash
git clone [https://github.com/erbeiithings/VMS-MAS-IT.git](https://github.com/erbeiithings/VMS-MAS-IT.git)
cd vms-mas-it

2. Install Dependencies
Karena folder vendor diabaikan oleh Git, kamu wajib mengunduh pustaka sistemnya terlebih dahulu dengan menjalankan perintah ini:

Bash
composer install
3. Konfigurasi Environment (.env)
Aplikasi butuh file .env untuk pengaturan database dan kunci keamanan.

Salin file .env.example dan ubah namanya menjadi .env. Bisa dilakukan manual atau jalankan perintah:

Bash
cp .env.example .env
Buat kunci keamanan aplikasi (Application Key) dengan perintah:

Bash
php artisan key:generate
4. Setup Database
Buka aplikasi XAMPP Control Panel lalu klik Start pada Apache dan MySQL.

Buka browser dan akses http://localhost/phpmyadmin.

Buat database baru (kosong) dengan nama: vms_masit.

Buka file .env di VS Code, lalu pastikan pengaturan blok databasenya persis seperti ini:

Cuplikan kode
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms_masit
DB_USERNAME=root
DB_PASSWORD=
5. Jalankan Migrasi dan Seeding
Langkah ini akan membuat tabel-tabel di database secara otomatis beserta data bawaannya (akun tester). Jalankan perintah:

Bash
php artisan migrate:fresh --seed
6. Link Storage (Opsional tapi Penting)
Jika aplikasi menggunakan fitur upload gambar/dokumen, pastikan menghubungkan folder storage ke publik:

Bash
php artisan storage:link
7. Jalankan Server Aplikasi
Langkah terakhir, nyalakan server lokal bawaan Laravel:

Bash
php artisan serve
Buka browser dan akses alamat webnya di: http://127.0.0.1:8000