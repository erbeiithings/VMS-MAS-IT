🏢 MAS-IT VMS (Visit Management System)

MAS-IT VMS adalah Sistem Informasi berbasis web yang dirancang untuk mengelola, mencatat, dan memonitor data kunjungan (visit) serta aktivitas pekerjaan lapangan engineer. Sistem ini memudahkan lacak jadwal kunjungan, pelaporan hasil pekerjaan secara digital, dan proses persetujuan oleh tingkat Kepala maupun Pimpinan.

✨ Fitur Utama

Multi-Role Access & Authentication: Hak akses terpisah dengan proteksi sesi untuk Kepala Pimpinan, Pimpinan, dan Engineer.

Manajemen & Penjadwalan Kunjungan: Pencatatan jadwal tugas lapangan, penugasan Lead/Support Engineer, dan tracking status kunjungan.

Presensi & Geolocation: Validasi titik lokasi (GPS) untuk proses check-in dan check-out kunjungan lapangan.

Pelaporan & Ekspor PDF: Pengisian aktivitas pekerjaan, upload bukti fisik/dokumentasi, serta cetak laporan PDF (Barryvdh DomPDF).

Monitoring & Dashboard Analytics: Visualisasi KPI, distribusi status kunjungan, dan riwayat pekerjaan secara real-time.

User Management: Pengelolaan akun pengguna beserta kontrol status keaktifan (Aktif/Nonaktif).

💻 Tech Stack

Framework: Laravel 10

Bahasa Pemrograman: PHP (Minimal v8.1)

Database: MySQL

Styling & UI: Tailwind CSS (via CDN) & Custom Components

Pustaka Utama: barryvdh/laravel-dompdf (Generasi Laporan PDF)

🛠️ Persyaratan Sistem (Prerequisites)

Sebelum menjalankan project ini secara lokal, pastikan perangkat kamu sudah terinstal:

PHP: Versi >= 8.1

Composer: Untuk manajemen dependency PHP

MySQL Database Server: (via XAMPP, Laragon, atau MySQL Standalone)

Git: Untuk clone repository

🚀 Panduan Instalasi Lokal (Local Setup)

Ikuti langkah-langkah berikut secara berurutan untuk menjalankan aplikasi di lingkungan development:

1. Clone Repository

Buka terminal atau Command Prompt, lalu jalankan:

git clone https://github.com/erbeiithings/VMS-MAS-IT.git
cd VMS-MAS-IT


2. Install Dependencies

Unduh semua pustaka backend yang dibutuhkan menggunakan Composer:

composer install


> Note: Aplikasi ini menggunakan Tailwind CSS via CDN, sehingga tidak membutuhkan build process Node.js (npm install / npm run dev).

3. Konfigurasi Environment (.env)

Salin file .env.example menjadi .env:

cp .env.example .env


Generate Application Key:

php artisan key:generate


4. Setup Database

Nyalakan Service Apache dan MySQL di Control Panel (XAMPP/Laragon).

Buka phpMyAdmin (http://localhost/phpmyadmin) dan buat database baru bernama:
vms_masit

Sesuaikan konfigurasi database pada file .env kamu:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms_masit
DB_USERNAME=root
DB_PASSWORD=


5. Migrasi & Seeding Database

Jalankan perintah ini untuk membuat struktur tabel dan mengisi data awal (dummy/account testing):

php artisan migrate:fresh --seed


6. Link Storage Asset

Hubungkan direktori storage privat ke folder public agar file/foto dokumentasi yang diunggah dapat diakses di browser:

php artisan storage:link


7. Jalankan Local Development Server

Jalankan server lokal bawaan Laravel:

php artisan serve


Akses aplikasi melalui browser di alamat: http://127.0.0.1:8000

📱 Pengujian Fitur GPS di Perangkat HP (Jaringan Lokal)

Jika pengujian dilakukan melalui perangkat HP dalam satu jaringan WiFi:

Jalankan server dengan menyertakan host IP:

php artisan serve --host=0.0.0.0 --port=8000


Cek IP lokal laptop kamu (via ipconfig di CMD).

Akses via browser HP menggunakan alamat http://<IP_LAPTOP>:8000.

Jika browser HP memblokir fitur lokasi/GPS karena koneksi HTTP, aktifkan flag "Insecure origins treated as secure" pada chrome://flags atau brave://flags untuk URL IP lokal kamu.