<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\FormatNomor;

/**
 * Data transaksi demo agar aplikasi terlihat sudah berjalan dipakai beneran:
 * kunjungan (berbagai status), laporan, aktivitas check-in/out, pengeluaran,
 * peminjaman tools, support engineer, dan tools per kunjungan.
 *
 * Idempotent: aman dijalankan ulang (skip kalau nomor kunjungan sudah ada).
 */
class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $eng = fn(string $username) => DB::table('engineers')
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'engineers.id_pengguna')
            ->where('pengguna.username', $username)->value('engineers.id_engineer');

        $cust = fn(string $nama) => DB::table('customers')->where('nama_perusahaan', $nama)->value('id_customer');
        $site = fn(string $namaCabang) => DB::table('customer_sites')->where('nama_cabang', $namaCabang)->first();
        $tool = fn(string $nama) => DB::table('tools')->where('nama_alat', $nama)->first();

        $andi  = $eng('andi.engineer');
        $siti  = $eng('siti.engineer');
        $joko  = $eng('joko.engineer');
        $putri = $eng('putri.engineer');
        $idPimpinan = DB::table('pengguna')->where('username', 'pimpinan')->value('id_pengguna');

        // ================= DAFTAR KUNJUNGAN =================
        // [customer, site, lead, [support], tanggal, waktu, pekerjaan, patokan, status, alasan_reschedule]
        $daftar = [
            // --- Selesai (6) ---
            ['PT. Maju Jaya Abadi', 'Kantor Pusat Jakarta', $andi, [$siti], '2026-09-14', '09:00', 'Maintenance rutin server & jaringan', 'Gedung A lantai 3, ruang server', 'Selesai', null],
            ['PT. Sinar Terang Perkasa', 'Kantor Pusat Bandung', $siti, [], '2026-09-16', '10:00', 'Troubleshooting koneksi internet lambat', 'Lobi utama, tanya ke resepsionis', 'Selesai', null],
            ['PT. Berkah Alam Nusantara', 'Gudang Sidoarjo', $joko, [$putri], '2026-09-21', '08:30', 'Instalasi access point baru', 'Gudang blok C, pintu samping', 'Selesai', null],
            ['PT. Cahaya Digital Indonesia', 'Data Center Cibitung', $andi, [], '2026-09-23', '09:00', 'Penggantian harddisk server', 'Gerbang MM2100, tunjukkan KTP ke security', 'Selesai', null],
            ['PT. Maju Jaya Abadi', 'Cabang Bekasi', $putri, [], '2026-09-28', '13:00', 'Perbaikan printer network', 'Ruko deretan Alfamart', 'Selesai', null],
            ['PT. Sinar Terang Perkasa', 'Cabang Cimahi', $joko, [], '2026-09-30', '09:30', 'Penarikan kabel UTP baru', null, 'Selesai', null],
            // --- Dikerjakan (2) ---
            ['PT. Cahaya Digital Indonesia', 'Head Office Thamrin', $siti, [$andi], '2026-10-02', '09:00', 'Konfigurasi ulang router & firewall', 'Tower B lantai 12', 'Dikerjakan', null],
            ['PT. Berkah Alam Nusantara', 'Kantor Pusat Surabaya', $joko, [], '2026-10-01', '10:00', 'Audit & dokumentasi infrastruktur IT', null, 'Dikerjakan', null],
            // --- Terjadwal / Dikonfirmasi (3) ---
            ['PT. Tunas Bangsa Sejahtera', 'Kantor Pusat Medan', $andi, [], '2026-10-05', '09:00', 'Instalasi ulang OS + aplikasi standar', null, 'Terjadwal', null],
            ['PT. Maju Jaya Abadi', 'Cabang Depok', $putri, [$joko], '2026-10-07', '10:00', 'Pemasangan CCTV tambahan', 'Sebelah kampus, parkir di basement', 'Dikonfirmasi', null],
            ['PT. Sinar Terang Perkasa', 'Kantor Pusat Bandung', $siti, [], '2026-10-09', '09:00', 'Upgrade RAM workstation', null, 'Terjadwal', null],
            // --- Reschedule (1) ---
            ['PT. Berkah Alam Nusantara', 'Cabang Gresik', $joko, [], '2026-10-06', '09:00', 'Setting VLAN & segmentasi jaringan', null, 'Reschedule', 'Customer meminta penjadwalan ulang karena ruang server sedang renovasi sampai awal Oktober.'],
        ];

        $hasilPekerjaan = [
            'Maintenance server selesai. Dilakukan pembersihan fisik, update patch keamanan, dan pengecekan status RAID. Seluruh layanan berjalan normal, suhu ruang server stabil.',
            'Koneksi internet sudah normal kembali. Ditemukan kabel backbone tertekuk di rak server; kabel diganti dan kecepatan kembali ke 98 Mbps (sesuai paket).',
            'Terpasang 3 unit access point baru di area gudang blok C. Sinyal WiFi merata, sudah diuji dari 12 titik dan semuanya terhubung dengan baik.',
            'Harddisk server yang rusak berhasil diganti. Rebuild RAID selesai tanpa kehilangan data. Sistem kembali online dan sudah dimonitor 2x24 jam.',
            'Printer network sudah bisa dipakai semua user. Driver diinstal ulang di 8 PC dan antrean cetak yang macet dibersihkan.',
            'Penarikan 120 meter kabel UTP Cat6 selesai ke 14 titik. Semua titik dites dengan LAN tester dan lolos (koneksi gigabit terdeteksi).',
        ];

        $catatanTambahan = [
            'Disarankan penggantian baterai UPS dalam 3 bulan ke depan.',
            null,
            'Customer meminta penambahan 2 AP lagi untuk area parkir — dibuatkan penawaran terpisah.',
            null,
            'Tinta cadangan disarankan distok karena pemakaian tinggi.',
            null,
        ];

        $toolsPerKunjungan = [
            ['Laptop Service Lenovo ThinkPad', 'LAN Cable Tester'],
            ['Laptop Service HP ProBook', 'LAN Cable Tester'],
            ['Laptop Service Lenovo ThinkPad', 'Obeng Set Presisi 32pcs', 'Multimeter Digital'],
            ['Laptop Service Lenovo ThinkPad', 'Obeng Set Presisi 32pcs'],
            ['Laptop Service HP ProBook'],
            ['LAN Cable Tester', 'Kabel UTP Cat6 50m'],
            ['Laptop Service Lenovo ThinkPad', 'USB to Serial Adapter'],
            ['Laptop Service HP ProBook', 'Multimeter Digital'],
            ['Laptop Service Lenovo ThinkPad'],
            ['Laptop Service HP ProBook', 'Obeng Set Presisi 32pcs', 'Multimeter Digital'],
            ['Laptop Service Lenovo ThinkPad', 'Obeng Set Presisi 32pcs'],
            ['Laptop Service HP ProBook', 'USB to Serial Adapter'],
        ];

        $pengeluaranTemplate = [
            [['Bensin', 75000, 'BBM PP kantor - lokasi'], ['Tol', 27000, 'Tol dalam kota'], ['Parkir', 10000, 'Parkir gedung']],
            [['Bensin', 50000, 'BBM'], ['Parkir', 8000, null]],
            [['Bensin', 60000, 'BBM PP'], ['Tol', 35000, 'Tol Waru'], ['Makan', 45000, 'Makan siang 2 orang']],
            [['Bensin', 85000, 'BBM PP Cibitung'], ['Tol', 48000, 'Tol Cikampek']],
            [['Bensin', 40000, 'BBM'], ['Parkir', 5000, null]],
            [['Bensin', 55000, 'BBM'], ['Tol', 20000, null]],
        ];

        $idxSelesai = 0;
        foreach ($daftar as $i => [$namaCust, $namaSite, $lead, $supports, $tanggal, $waktu, $pekerjaan, $patokan, $status, $alasan]) {
            $idCust = $cust($namaCust);
            $siteRow = $site($namaSite);
            if (!$idCust || !$siteRow) continue;

            $nomor = FormatNomor::generate('kunjungan');

            // Idempotent: skip jika pekerjaan+tanggal+customer sudah ada
            $sudahAda = DB::table('kunjungan')
                ->where('id_customer', $idCust)
                ->where('tanggal', $tanggal)
                ->where('pekerjaan', $pekerjaan)->exists();
            if ($sudahAda) continue;

            $idKunjungan = DB::table('kunjungan')->insertGetId([
                'nomor' => $nomor,
                'id_customer' => $idCust,
                'id_site' => $siteRow->id_site,
                'id_engineer' => $lead,
                'tanggal' => $tanggal,
                'waktu' => $waktu . ':00',
                'lokasi' => $siteRow->alamat_lengkap,
                'patokan' => $patokan,
                'pekerjaan' => $pekerjaan,
                'status' => $status,
                'alasan_reschedule' => $alasan,
                'created_at' => $tanggal . ' 07:00:00',
                'updated_at' => now(),
            ]);

            // Tools yang dibawa
            foreach ($toolsPerKunjungan[$i] as $namaTool) {
                $t = $tool($namaTool);
                if ($t) {
                    DB::table('kunjungan_tool')->insert([
                        'id_kunjungan' => $idKunjungan,
                        'id_tool' => $t->id_tool,
                        'jumlah' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Support engineers
            foreach ($supports as $idSup) {
                DB::table('kunjungan_support')->insert([
                    'id_kunjungan' => $idKunjungan,
                    'id_engineer' => $idSup,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // Konfirmasi kehadiran lead + support
            $semuaEngineer = array_unique(array_merge([$lead], $supports));
            foreach ($semuaEngineer as $idEng) {
                DB::table('kunjungan_konfirmasi')->insert([
                    'id_kunjungan' => $idKunjungan,
                    'id_engineer' => $idEng,
                    'status' => $status === 'Terjadwal' ? 'menunggu' : 'diterima',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Aktivitas check-in/out untuk Selesai & Dikerjakan
            if (in_array($status, ['Selesai', 'Dikerjakan'])) {
                $checkin = $tanggal . ' ' . $waktu . ':00';
                $dataAktivitas = [
                    'id_kunjungan' => $idKunjungan,
                    'id_engineer' => $lead,
                    'waktu_mulai' => $checkin,
                    'lokasi' => $siteRow->latitude . ',' . $siteRow->longitude,
                    'deskripsi' => 'Check-in di lokasi: ' . $pekerjaan,
                    'created_at' => $checkin,
                    'updated_at' => now(),
                ];
                if ($status === 'Selesai') {
                    $checkout = $tanggal . ' ' . date('H:i:s', strtotime($waktu . ' +4 hours'));
                    $dataAktivitas['waktu_selesai'] = $checkout;
                    $dataAktivitas['lokasi_checkout'] = $siteRow->latitude . ',' . $siteRow->longitude;
                    $dataAktivitas['catatan'] = 'Pekerjaan selesai, sudah diverifikasi customer.';
                }
                DB::table('aktivitas_pekerjaan')->insert($dataAktivitas);

                // Laporan untuk yang Selesai
                if ($status === 'Selesai') {
                    $disetujui = $idxSelesai < 4; // 4 disetujui, 2 menunggu
                    DB::table('laporan')->insert([
                        'id_kunjungan' => $idKunjungan,
                        'id_engineer_pembuat' => $lead,
                        'tanggal_dibuat' => $tanggal . ' 15:00:00',
                        'status_laporan' => 'Terbuat Otomatis',
                        'hasil_pekerjaan' => $hasilPekerjaan[$idxSelesai],
                        'catatan_tambahan' => $catatanTambahan[$idxSelesai],
                        'status_approval' => $disetujui ? 'Disetujui' : 'Menunggu Persetujuan',
                        'disetujui_oleh' => $disetujui ? $idPimpinan : null,
                        'catatan_approval' => $disetujui ? 'Hasil pekerjaan sudah sesuai, terima kasih.' : null,
                        'created_at' => $tanggal . ' 15:00:00',
                        'updated_at' => now(),
                    ]);
                    // Pengeluaran
                    foreach ($pengeluaranTemplate[$idxSelesai] as [$jenis, $nominal, $ket]) {
                        DB::table('pengeluaran')->insert([
                            'id_kunjungan' => $idKunjungan,
                            'jenis_biaya' => $jenis,
                            'nominal' => $nominal,
                            'keterangan' => $ket,
                            'created_at' => $tanggal . ' 16:00:00',
                            'updated_at' => now(),
                        ]);
                    }
                    $idxSelesai++;
                }
            }
        }

        // ================= PEMINJAMAN TOOLS =================
        $pinjam = [
            // [tool, engineer_username, tgl_pinjam, tgl_kembali, status, kondisi_kembali]
            ['Laptop Service Lenovo ThinkPad', 'andi.engineer', '2026-10-01 08:00:00', null, 'Dipinjam', null],
            ['LAN Cable Tester', 'siti.engineer', '2026-09-30 09:00:00', null, 'Dipinjam', null],
            ['Multimeter Digital', 'joko.engineer', '2026-10-02 08:30:00', null, 'Dipinjam', null],
            ['Obeng Set Presisi 32pcs', 'putri.engineer', '2026-09-28 10:00:00', null, 'Dipinjam', null],
            ['USB to Serial Adapter', 'andi.engineer', '2026-09-20 08:00:00', '2026-09-25 17:00:00', 'Dikembalikan', 'Baik'],
            ['Harddisk Eksternal 1TB', 'siti.engineer', '2026-09-18 09:00:00', '2026-09-22 16:30:00', 'Dikembalikan', 'Baik'],
            ['Kabel UTP Cat6 50m', 'joko.engineer', '2026-09-15 08:00:00', '2026-09-21 17:00:00', 'Dikembalikan', 'Baik'],
        ];

        foreach ($pinjam as [$namaTool, $username, $tglPinjam, $tglKembali, $status, $kondisi]) {
            $t = $tool($namaTool);
            $idEng = $eng($username);
            if (!$t || !$idEng) continue;
            $sudahAda = DB::table('peminjaman_tools')
                ->where('id_tool', $t->id_tool)
                ->where('id_engineer', $idEng)
                ->where('tanggal_pinjam', $tglPinjam)->exists();
            if ($sudahAda) continue;
            DB::table('peminjaman_tools')->insert([
                'id_tool' => $t->id_tool,
                'id_engineer' => $idEng,
                'jumlah' => 1,
                'tanggal_pinjam' => $tglPinjam,
                'tanggal_kembali' => $tglKembali,
                'status' => $status,
                'kondisi_kembali' => $kondisi,
                'keterangan' => $status === 'Dipinjam' ? 'Dipinjam untuk pekerjaan lapangan' : null,
                'created_at' => $tglPinjam,
                'updated_at' => now(),
            ]);
        }
    }
}
