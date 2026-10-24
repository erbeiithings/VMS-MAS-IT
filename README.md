# MAS-IT VMS (Visit Management System)

Halo, ini MAS-IT VMS. Aplikasi web buat ngatur kunjungan engineer ke lokasi customer, dari awal dijadwalkan sampai laporannya jadi PDF dan ditandatangani.

Singkatnya, kalau dulu jadwal, laporan, dan tools engineer tersebar di chat dan catatan lepas, di sini semuanya ketemu di satu tempat: siapa yang ditugaskan, ke mana, ngapain aja di lapangan, habis berapa, dan laporannya sudah disetujui atau belum.

> Catatan penting: repo ini dipakai sebagai jejak dan arsip kode. Situs live tidak otomatis berubah kalau repo ini diubah, deploy ke production dilakukan terpisah.

## Fitur utamanya

### Jadwal dan penugasan

- Buat jadwal kunjungan: pilih customer, site atau cabang, tanggal dan waktu, pekerjaan, patokan lokasi, lead engineer, tim support sampai 4 orang, dan tools yang dibawa.
- Nomor kunjungan dibuat otomatis, contohnya `vmsmit26001`. Customer, tool, dan engineer juga punya kode otomatis sendiri (`cst...`, `tls...`, `eng...`).
- Engineer yang ditugaskan bisa Terima atau Tolak. Kalau ditolak, Pimpinan yang memutuskan: ACC penolakan lalu ganti engineer, atau tolak penolakannya supaya tugas tetap jalan.

### Kerja di lapangan

- Check-in dan check-out pakai GPS, jadi waktu dan lokasi tercatat otomatis.
- Upload foto dokumentasi langsung dari HP, lengkap dengan kategorinya.
- Catat pengeluaran operasional, lengkap dengan jenis biaya, nominal, keterangan, dan bukti nota.
- Status kunjungan jelas: Terjadwal, Dikonfirmasi, Dikerjakan, Selesai, dan Reschedule.

### Laporan dan approval

- Laporan dibuat dalam 2 bagian: isi data laporan, lalu tanda tangan customer dan engineer.
- Bisa juga lewati tanda tangan, tapi wajib isi alasan.
- Draft laporan dan draft tanda tangan tersimpan otomatis, jadi aman kalau halaman ke-refresh.
- Setelah selesai, laporan masuk ke approval: Disetujui, Revisi, atau Ditolak oleh Pimpinan atau Kepala Pimpinan.
- Kalau Revisi, engineer isi ulang laporannya dan statusnya balik jadi Menunggu Persetujuan. Kalau sudah Disetujui, laporan terkunci dan tidak bisa direvisi lagi.
- Laporan bisa dicetak jadi PDF dan dikirim ke klien lewat email.

### Tools dan master data

- Inventaris tools lengkap dengan stok total, stok terkini, dan kondisi alat.
- Engineer bisa pinjam tools buat keperluan lain dan mengembalikannya lewat menu Tools Saya, dengan kondisi Baik, Rusak Ringan, atau Rusak Berat.
- Master data yang dikelola: Data Pengguna, Data Customer, Data Site, Tools dan Alat Kerja, Format Nomor, dan Opsi Formulir.
- Opsi Formulir dipakai buat ngatur daftar Jenis Biaya dan Kategori Foto yang muncul di formulir lapangan.

### Dashboard per role

Setiap role punya dashboardnya sendiri, jadi yang dilihat langsung sesuai kerjaannya:

| Role | Fokusnya | Yang bisa dilakukan |
| --- | --- | --- |
| Kepala Pimpinan | Monitoring dan analisis strategis | Lihat semua data, kelola Data Pengguna termasuk Kelola Pimpinan, hapus kunjungan, approval laporan |
| Pimpinan | Operasional dan penjadwalan | Buat jadwal, ganti engineer, ACC atau tolak penolakan, approval laporan, tandai pengeluaran sudah atau belum diganti |
| Engineer | Area tugas lapangan | Lihat tugasnya sendiri, konfirmasi tugas, check-in dan check-out GPS, upload foto, catat pengeluaran, isi laporan, pinjam dan kembalikan tools |

Yang perlu diingat soal menu:

- Admin dan Pimpinan menunya sama persis, tapi Pimpinan tidak melihat tombol Hapus di daftar Kunjungan dan tidak melihat tombol Kelola Pimpinan di Data Pengguna.
- Engineer tidak punya menu Master Data sama sekali. Di Kunjungan, Engineer tidak punya tombol Buat Jadwal dan Hapus. Di Laporan, Engineer tidak punya tombol Revisi.
- Profil untuk semua role hanya lewat dropdown akun, bukan menu utama.

## Alurnya, dari awal sampai selesai

1. Pimpinan atau Kepala Pimpinan buat jadwal kunjungan.
2. Sistem bikin nomor kunjungan otomatis, statusnya Terjadwal.
3. Engineer Terima atau Tolak penugasan.
4. Engineer check-in GPS di lokasi, status jadi Dikerjakan.
5. Engineer kerja, upload foto, dan catat pengeluaran.
6. Engineer isi laporan, lalu customer dan engineer tanda tangan (atau lewati tanda tangan dengan alasan).
7. Engineer check-out GPS, status jadi Selesai.
8. Pimpinan atau Kepala Pimpinan approval laporan: Disetujui, Revisi, atau Ditolak.
9. Kalau Disetujui, laporan bisa dicetak PDF dan dikirim ke klien. Engineer mengembalikan tools yang dipakai.

Pengecualian yang juga sudah ditangani: Reschedule kalau jadwal perlu diatur ulang, Force Selesai dari Pimpinan atau Kepala kalau engineer lupa check-out, revisi tools yang terkunci setelah check-in, dan edit atau hapus foto dan pengeluaran oleh yang berwenang.

## Tech stack

- Framework: Laravel 10
- Bahasa: PHP minimal 8.1
- Database: MySQL
- UI: Tailwind CSS lewat CDN, jadi tidak perlu build Node.js
- PDF: barryvdh/laravel-dompdf
- Email laporan ke klien: Resend API (opsional, lihat bagian konfigurasi)

## Cara jalanin di laptop kamu

### Yang perlu disiapkan dulu

- PHP minimal 8.1
- Composer
- MySQL, bisa lewat XAMPP, Laragon, atau MySQL standalone
- Git

### Langkah-langkahnya

```bash
git clone https://github.com/erbeiithings/VMS-MAS-IT.git
cd VMS-MAS-IT
composer install
```

Salin file environment, lalu generate key-nya:

```bash
cp .env.example .env
php artisan key:generate
```

Buat database baru, misalnya `vms_masit`, lalu sesuaikan isi `.env` kamu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms_masit
DB_USERNAME=root
DB_PASSWORD=
```

Isi `DB_PASSWORD` sesuai password MySQL di laptop kamu, jangan dikosongkan kalau MySQL kamu pakai password.

Terus jalankan migrasi dan seeder bawaan:

```bash
php artisan migrate:fresh --seed
```

Seeder bawaan ini mengisi role (Kepala Pimpinan, Pimpinan, Engineer), akun admin dan pimpinan, dan format nomor awal. Kalau mau data contoh yang lebih lengkap, ada seeder tambahan yang bisa dijalankan manual:

```bash
php artisan db:seed --class=DummyDataSeeder
php artisan db:seed --class=DemoTransactionSeeder
```

Terakhir, hubungkan storage dan jalankan servernya:

```bash
php artisan storage:link
php artisan serve
```

Buka di browser: http://127.0.0.1:8000

### Konfigurasi tambahan

- **Kirim email laporan:** isi `RESEND_API_KEY` di `.env` kalau mau fitur Kirim Klien jalan. Kalau key ini tidak diisi, pengiriman email akan gagal dengan pesan error, tapi fitur lainnya tetap jalan.
- **Upload foto dan dokumen:** `php artisan storage:link` itu wajib, supaya file yang diupload bisa dibuka dari browser.
- **GPS di HP:** kalau mau ngetes dari HP yang satu WiFi dengan laptop, jalankan server dengan host terbuka:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Cek IP lokal laptop kamu (di Windows lewat `ipconfig`), lalu buka dari browser HP ke `http://<IP_LAPTOP>:8000`. Kalau browser HP memblokir GPS karena koneksinya HTTP, aktifkan flag "Insecure origins treated as secure" di `chrome://flags` atau `brave://flags` untuk URL IP lokal kamu.

## Struktur proyek, biar gampang nyari file

```text
app/Http/Controllers/   # Logika tiap halaman: kunjungan, laporan, master data, tools, profil
app/Models/             # Model: pengguna, engineer, customer, kunjungan, laporan, dan kawan-kawan
database/migrations/    # Struktur tabel, 38 migrasi
database/seeders/       # Data awal: role, akun, format nomor, data contoh
resources/views/        # Tampilan Blade: auth, dashboard, kunjungan, laporan, master, peminjaman, profile
routes/web.php          # Semua route web, lengkap dengan pembatasan role
public/                 # File yang bisa diakses browser: index.php, favicon, logo, uploads
```

## Keamanan, tolong dibaca dulu

- Jangan pernah commit file `.env` asli ke repo. File itu isinya kredensial production dan tidak boleh tersebar.
- Jangan upload dump database production ke repo. Isinya data asli customer dan pengguna.
- Setelah install dan seed di lingkungan baru, segera ganti password akun bawaan lewat menu Profil, dan buat password tiap role berbeda, jangan disamakan.
- Username hanya bisa diubah 1 kali dalam 7 hari, jadi pikir dulu sebelum ganti.
- Halaman yang dibatasi role akan menampilkan 403 Akses Ditolak kalau dibuka oleh role yang tidak berhak, itu memang sengaja.

## Mau ikut ngembangin?

Silakan. Alur paling aman: clone repo, jalanin lokal dulu, ubah di branch baru, tes login ketiga role (Kepala Pimpinan, Pimpinan, Engineer) sebelum push. Kalau ada yang bingung soal alur bisnisnya, cek dokumen analisis di repo atau tanya langsung ke pemilik proyek, jangan nebak-nebak dari nama tabel saja, karena beberapa halaman di aplikasi ini memakai kode di URL (nomor tiket, kode customer, dan kode tool), bukan ID angka.
