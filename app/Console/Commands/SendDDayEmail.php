<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kunjungan;
use Illuminate\Support\Facades\Mail;

class SendDDayEmail extends Command
{
    // Ini nama perintah yang nanti diketik di terminal
    protected $signature = 'app:send-dday-email';
    protected $description = 'Kirim email pengingat (D-Day Alert) untuk jadwal kunjungan hari ini';

    public function handle()
    {
        $hariIni = now()->toDateString();
        
        // Cari semua kunjungan yang dijadwalkan HARI INI
        $kunjunganHariIni = Kunjungan::with(['customer', 'engineer.user', 'supportEngineers.user'])
            ->whereDate('tanggal', $hariIni)
            ->where('status', 'Terjadwal')
            ->get();

        $this->info("Menemukan {$kunjunganHariIni->count()} jadwal untuk hari ini.");

        foreach ($kunjunganHariIni as $kunjungan) {
            $pesan = "PENGINGAT HARI H!\n\n";
            $pesan .= "Nomor Tiket: {$kunjungan->nomor}\n";
            $pesan .= "Pekerjaan: {$kunjungan->pekerjaan}\n";
            $pesan .= "Customer: {$kunjungan->customer->nama_perusahaan}\n";
            $pesan .= "Waktu: {$kunjungan->waktu}\n\n";
            $pesan .= "Silakan login ke sistem VMS MAS-IT untuk detail lebih lanjut dan melakukan Check-In GPS di lokasi.";

            // Kirim email ke Lead Engineer (kalau ada emailnya di DB pengguna)
            if ($kunjungan->engineer && $kunjungan->engineer->user->email) {
                Mail::raw($pesan, function ($message) use ($kunjungan) {
                    $message->to($kunjungan->engineer->user->email)
                            ->subject('D-Day Alert: ' . $kunjungan->nomor);
                });
                $this->info("Email terkirim ke Lead: " . $kunjungan->engineer->user->email);
            }

            // Kirim email ke Tim Support
            foreach ($kunjungan->supportEngineers as $support) {
                if ($support->user->email) {
                    Mail::raw($pesan, function ($message) use ($support, $kunjungan) {
                        $message->to($support->user->email)
                                ->subject('D-Day Alert (Support): ' . $kunjungan->nomor);
                    });
                    $this->info("Email terkirim ke Support: " . $support->user->email);
                }
            }
        }

        $this->info("Selesai memproses email D-Day Alert.");
    }
}