<?php
// app/Services/PDFService.php
namespace App\Services;

use App\Models\SuratKuasa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PDFService
{

    public function generateSuratKuasaPDF(SuratKuasa $suratKuasa)
    {

        // Set locale dan ambil komponen tanggal
        $created = Carbon::parse($suratKuasa->created_at)->locale('id');
        Carbon::setLocale('id');

        $hari_pembuatan = $created->translatedFormat('l');
        $tanggal = $created->translatedFormat('d');
        $bulan = $created->translatedFormat('F');
        $tahun = $created->translatedFormat('Y');
        $tanggal_pembuatan = $created->translatedFormat('d F Y');

        // Buat string QR Code yang mudah dibaca manusia
        $qrCodeString =
            "ID Pengajuan: {$suratKuasa->id}\n" .
            "Nama Pemberi: {$suratKuasa->nama_pemberi}\n" .
            "Nama Penerima: {$suratKuasa->nama_penerima}\n" .
            "Tanggal Pembuatan: {$tanggal_pembuatan}\n" .

            // Generate QR Code dengan error handling
            $qrcode = null;
        try {
            Log::info('Attempting to generate QR code...');

            $qrImageData = QrCode::format('png')
                ->size(400) // Ukuran QR
                ->margin(1)
                ->errorCorrection('H')
                ->generate($qrCodeString);

            $qrcode = 'data:image/png;base64,' . base64_encode($qrImageData);

            Log::info('QR Code generated successfully');
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            $qrcode = null; // Pastikan template handle ini
        }

        $data = [
            'surat_kuasa'            => $suratKuasa,
            'tanggal_pembuatan'      => $tanggal_pembuatan,
            'hari_pembuatan'         => $hari_pembuatan,
            'tanggal'                => $tanggal,
            'bulan'                  => $bulan,
            'tahun'                   => $tahun,
            'qrcode'                  => $qrcode,
            'nama_penerima_short'    => $this->shortenNameWithInitialsRobust($suratKuasa->nama_penerima),
            'nama_pemberi_short'     => $this->shortenNameWithInitialsRobust($suratKuasa->nama_pemberi),
        ];


        try {
            // Generate PDF
            $pdf = PDF::loadView('pdf.surat-kuasa', $data)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont'           => 'Times New Roman',
                    'isHtml5ParserEnabled'  => true,
                    'isPhpEnabled'          => true,
                    'fontHeightRatio'       => 1.1,
                    'dpi'                   => 150,
                ]);

            // Simpan PDF
            $fileName = 'surat-kuasa-' . $suratKuasa->id . '-' . time() . '.pdf';
            $filePath = 'pdf/' . $fileName;
            Storage::disk('public')->put($filePath, $pdf->output());

            // Update record
            $suratKuasa->update([
                'pdf_file' => $filePath
            ]);

            Log::info('PDF generated successfully: ' . $filePath);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function shortenNameWithInitialsRobust($name)
    {
        // Validasi input
        if (!is_string($name) || empty(trim($name))) {
            return $name;
        }

        $name = trim(preg_replace('/\s+/', ' ', $name)); // Normalize multiple spaces

        // Jika nama tidak lebih dari 20 karakter, return as is
        if (strlen($name) <= 15) {
            return $name;
        }

        // Pisah nama berdasarkan spasi
        $parts = explode(' ', $name);
        $parts = array_filter($parts); // Hapus elemen kosong

        // Jika hanya satu kata, return as is
        if (count($parts) <= 1) {
            return $name;
        }

        // Ambil nama depan
        $firstName = array_shift($parts);

        // Buat inisial untuk nama belakang
        $initials = array_map(function ($part) {
            return strtoupper(substr($part, 0, 1)) . '.';
        }, $parts);

        return $firstName . ' ' . implode(' ', $initials);
    }

    public function generateSuratKuasaPDF1(SuratKuasa $suratKuasa)
    {

        // Set locale dan ambil komponen tanggal
        $created = Carbon::parse($suratKuasa->created_at)->locale('id');
        Carbon::setLocale('id');

        $hari_pembuatan = $created->translatedFormat('l');
        $tanggal = $created->translatedFormat('d');
        $bulan = $created->translatedFormat('F');
        $tahun = $created->translatedFormat('Y');
        $tanggal_pembuatan = $created->translatedFormat('d F Y');

        // Data untuk QR code
        $qrCodeData = [
            'id'                => $suratKuasa->id,
            'nama_pemberi'      => $suratKuasa->nama_pemberi,
            'nama_penerima'     => $suratKuasa->nama_penerima,
            'tanggal_pembuatan' => $tanggal_pembuatan,
            'hash'              => hash('sha256', $suratKuasa->id . $suratKuasa->nama_pemberi . $suratKuasa->created_at)
        ];

        // Generate QR Code dengan error handling
        $qrcode = null;
        try {
            Log::info('Attempting to generate QR code...');

            $qrImageData = QrCode::format('png')
                ->size(400) // Ukuran lebih kecil karena akan ditempatkan di bawah
                ->margin(1)
                ->errorCorrection('H')
                ->generate(json_encode($qrCodeData));

            $qrcode = 'data:image/png;base64,' . base64_encode($qrImageData);

            Log::info('QR Code generated successfully');
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());

            // Set QR code ke null - template harus handle ini
            $qrcode = null;
        }

        // Data untuk view (tanpa barcode)
        $data = [
            'surat_kuasa'       => $suratKuasa,
            'nomor_surat'       => $nomor_surat,
            'tanggal_pembuatan' => $tanggal_pembuatan,
            'hari_pembuatan'    => $hari_pembuatan,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'qrcode'            => $qrcode,
        ];

        try {
            // Generate PDF dengan margin yang disesuaikan
            $pdf = PDF::loadView('pdf.surat-kuasa', $data)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont'           => 'Times New Roman',
                    'isHtml5ParserEnabled'  => true,
                    'isPhpEnabled'          => true,
                    'debugKeepTemp'         => false,
                    'debugPng'              => false,
                    'debugCss'              => false,
                    'fontHeightRatio'       => 1.1,
                    'dpi'                   => 150,
                ]);

            // Simpan PDF
            $fileName = 'surat-kuasa-' . $suratKuasa->id . '-' . time() . '.pdf';
            $filePath = 'pdf/' . $fileName;
            Storage::disk('public')->put($filePath, $pdf->output());

            // Update record
            $suratKuasa->update([
                'pdf_file' => $filePath
            ]);

            Log::info('PDF generated successfully: ' . $filePath);

            return $filePath;
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
