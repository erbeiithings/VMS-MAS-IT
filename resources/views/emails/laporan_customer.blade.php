<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan MAS-IT</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    
    <h2>Halo, Bapak/Ibu {{ $kunjungan->customer->pic ?? 'PIC' }}</h2>
    
    <p>Terlampir Berita Acara (Service Completion Receipt) resmi dari PT. MAS-IT untuk tiket kunjungan <strong>{{ $kunjungan->nomor }}</strong>.</p>
    
    <p>
        <strong>Rincian Pekerjaan:</strong> {{ $kunjungan->pekerjaan }}<br>
        <strong>Tanggal Selesai:</strong> {{ $kunjungan->aktivitas->last()->waktu_selesai ?? $kunjungan->tanggal }}<br>
        <strong>Engineer Bertugas:</strong> {{ $kunjungan->engineer->user->nama ?? '-' }}
    </p>

    <p>Dokumen PDF terlampir pada email ini telah disetujui secara internal oleh atasan kami dan telah ditandatangani secara digital. Silakan diunduh untuk keperluan arsip perusahaan Anda.</p>

    <br>
    <p>Terima kasih atas kepercayaannya,</p>
    <p><strong>Tim MAS-IT</strong></p>

</body>
</html>