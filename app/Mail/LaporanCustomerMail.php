<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class LaporanCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $kunjungan;
    public $pdfData;

    // Kita butuh data kunjungan dan data file PDF-nya
    public function __construct($kunjungan, $pdfData)
    {
        $this->kunjungan = $kunjungan;
        $this->pdfData = $pdfData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Ini judul/subjek email yang bakal masuk ke HP Klien
            subject: 'Berita Acara Penyelesaian Pekerjaan - ' . $this->kunjungan->nomor,
        );
    }

    public function content(): Content
    {
        return new Content(
            // Ini nama file view HTML buat isi teks emailnya
            view: 'emails.laporan_customer',
        );
    }

    public function attachments(): array
    {
        // Fitur canggih Laravel buat attach file PDF langsung dari memory (tanpa perlu disave ke folder)
        return [
            Attachment::fromData(fn () => $this->pdfData, 'Laporan_MAS-IT_' . $this->kunjungan->nomor . '.pdf')
                    ->withMime('application/pdf'),
        ];
    }
}