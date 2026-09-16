<!DOCTYPE html>
<html>
<head>
    <title>Berita Acara Kunjungan - MAS-IT</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Import Font Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        body { 
            font-family: 'Poppins', Arial, sans-serif; 
            background-color: #f1f5f9; 
            margin: 0; 
            padding: 40px 20px; 
            color: #334155; 
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 10px 25px rgba(0, 34, 102, 0.1); 
        }
        .header { 
            background: #002266; 
            background: linear-gradient(90deg, #002266 0%, #0044cc 100%); 
            padding: 30px 20px; 
            text-align: center; 
        }
        .header h1 { 
            color: #ffffff; 
            margin: 0; 
            font-size: 24px; 
            letter-spacing: 1px; 
        }
        .header p {
            color: #93c5fd;
            font-size: 12px;
            margin: 5px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content { 
            padding: 40px 30px; 
            line-height: 1.6; 
        }
        .content h2 { 
            color: #002266; 
            font-size: 18px; 
            margin-top: 0; 
        }
        .info-box { 
            background-color: #f8fafc; 
            border-left: 4px solid #0044cc; 
            padding: 20px; 
            margin: 25px 0; 
            border-radius: 0 8px 8px 0; 
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0044cc;
        }
        .info-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-box td {
            padding: 6px 0;
            font-size: 14px;
            vertical-align: top;
        }
        .info-box td:first-child {
            width: 40%;
            color: #64748b;
            font-weight: 600;
        }
        .info-box td:last-child {
            color: #002266;
            font-weight: 600;
        }
        .footer { 
            background-color: #f8fafc; 
            text-align: center; 
            padding: 20px; 
            font-size: 12px; 
            color: #64748b; 
            border-top: 1px solid #e2e8f0; 
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Gradasi Biru MAS-IT -->
        <div class="header">
            <h1>MAS-IT</h1>
            <p>Visit Management System</p>
        </div>
        
        <div class="content">
            <h2>Halo, Bapak/Ibu {{ $kunjungan->customer->pic ?? 'PIC' }}</h2>
            <p>Terlampir Berita Acara (Service Completion Receipt) resmi dari PT. MAS-IT Solusi Integrasi untuk tiket kunjungan dengan nomor seri <strong>{{ $kunjungan->nomor }}</strong>.</p>
            
            <!-- Kotak Informasi Detail Pekerjaan -->
            <div class="info-box">
                <table>
                    <tr>
                        <td>Rincian Pekerjaan</td>
                        <td>{{ $kunjungan->pekerjaan }}</td>
                    </tr>
                    <tr>
                        <td>Waktu Selesai</td>
                        <td>{{ $kunjungan->aktivitas->last()->waktu_selesai ?? $kunjungan->tanggal }}</td>
                    </tr>
                    <tr>
                        <td>Engineer Bertugas</td>
                        <td>{{ $kunjungan->engineer->user->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 14px; color: #475569;">
                Dokumen PDF terlampir pada email ini telah disetujui secara internal dan ditandatangani secara digital. Silakan diunduh untuk keperluan arsip administrasi perusahaan Anda.
            </p>

            <p style="margin-top: 30px; font-size: 14px;">
                Terima kasih atas kepercayaannya,<br>
                <strong style="color: #002266; font-size: 16px;">Tim Operasional MAS-IT</strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} PT MAS-IT Solusi Integrasi. All rights reserved.<br>
            Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak membalas email ini.
        </div>
    </div>
</body>
</html>