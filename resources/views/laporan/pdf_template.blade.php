<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Service Completion Receipt - {{ $kunjungan->nomor }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a202c;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 9px;
            color: #475569;
        }
        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #f1f5f9;
            padding: 6px;
            border-radius: 4px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signature-box {
            text-align: center;
            width: 45%;
            vertical-align: top;
        }
        .sign-img {
            height: 60px;
            margin: 5px 0;
        }
        .footer-note {
            margin-top: 25px;
            font-size: 8px;
            color: #64748b;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="title">PT MAS-IT SOLUSI INTEGRASI</div>
                <div class="subtitle">IT Infrastructure, Networking & Security Management Solutions</div>
                <div class="subtitle">Website: mas-it.id | Email: support@mas-it.id</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="font-size: 12px; font-weight: bold; color: #0284c7;">BUKTI PENYELESAIAN PEKERJAAN</div>
                <div style="font-size: 10px; font-family: monospace;">No: {{ $kunjungan->nomor }}</div>
                <div style="font-size: 9px; color: #64748b;">Tanggal Cetak: {{ date('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">SERVICE COMPLETION RECEIPT</div>

    <!-- Informasi Kunjungan & Customer -->
    <table class="info-table">
        <tr>
            <td style="width: 20%; font-weight: bold;">Perusahaan (Customer)</td>
            <td style="width: 30%;">: {{ $kunjungan->customer->nama_perusahaan ?? '-' }}</td>
            <td style="width: 20%; font-weight: bold;">Engineer Bertugas</td>
            <td style="width: 30%;">: {{ $kunjungan->engineer->user->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Person In Charge (PIC)</td>
            <td>: {{ $kunjungan->customer->pic ?? '-' }} ({{ $kunjungan->customer->telepon ?? '-' }})</td>
            <td style="font-weight: bold;">Kontak Engineer</td>
            <td>: {{ $kunjungan->engineer->kontak ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Tanggal & Waktu Jadwal</td>
            <td>: {{ $kunjungan->tanggal }} ({{ $kunjungan->waktu }})</td>
            <td style="font-weight: bold;">Status Pekerjaan</td>
            <td>: <strong>{{ strtoupper($kunjungan->status) }}</strong></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Lokasi Pekerjaan</td>
            <td colspan="3">: {{ $kunjungan->lokasi }}</td>
        </tr>
    </table>

    <!-- Ringkasan Pekerjaan & Waktu Aktual -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Item Pekerjaan</th>
                <th style="width: 35%;">Deskripsi & Catatan Akhir</th>
                <th style="width: 20%;">Waktu Mulai (GPS)</th>
                <th style="width: 20%;">Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">{{ $kunjungan->pekerjaan }}</td>
                <td>{{ $aktivitas->catatan ?? 'Pekerjaan telah diselesaikan sesuai dengan instruksi kerja.' }}</td>
                <td>
                    {{ $aktivitas->waktu_mulai ? date('d/m/Y H:i', strtotime($aktivitas->waktu_mulai)) : '-' }}<br>
                    <small style="color: #64748b; font-size: 8px;">GPS: {{ $aktivitas->lokasi ?? '-' }}</small>
                </td>
                <td>
                    {{ $aktivitas->waktu_selesai ? date('d/m/Y H:i', strtotime($aktivitas->waktu_selesai)) : '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Tools / Alat Kerja yang Digunakan -->
    <div style="font-weight: bold; margin-bottom: 5px; font-size: 10px;">DAFTAR ALAT / TOOLS YANG DIGUNAKAN:</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Kode Alat</th>
                <th style="width: 40%;">Nama Alat</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 15%;">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungan->tools as $index => $tool)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace;">{{ $tool->kode }}</td>
                    <td>{{ $tool->nama_alat }}</td>
                    <td>{{ $tool->kategori }}</td>
                    <td>{{ $tool->kondisi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b; font-style: italic;">Tidak ada alat terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Kolom Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <div>Dikerjakan Oleh,</div>
                <div style="font-weight: bold; margin-bottom: 45px;">Engineer MAS-IT</div>
                <div style="border-top: 1px solid #334155; display: inline-block; width: 80%; padding-top: 3px; font-weight: bold;">
                    {{ $kunjungan->engineer->user->nama ?? 'Engineer' }}
                </div>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                <div>Disetujui & Diverifikasi Oleh,</div>
                <div style="font-weight: bold; margin-bottom: 5px;">Customer / Klien</div>
                @if($bukti && $bukti->tanda_tangan_customer)
                    <div>
                        <img src="{{ $bukti->tanda_tangan_customer }}" class="sign-img" alt="Digital Signature">
                    </div>
                @else
                    <div style="height: 55px;"></div>
                @endif
                <div style="border-top: 1px solid #334155; display: inline-block; width: 80%; padding-top: 3px; font-weight: bold;">
                    {{ $kunjungan->customer->pic ?? 'Customer PIC' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini dibuat dan diverifikasi secara digital melalui Sistem Manajemen Kunjungan MAS-IT VMS.<br>
        Segala bentuk perubahan data setelah tanda tangan terverifikasi dianggap tidak sah.
    </div>

</body>
</html>