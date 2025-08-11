<?php
// app/Services/PDFService.php
namespace App\Services;

use App\Models\SuratKuasa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PDFService
{
    public function generateSuratKuasaPDF(SuratKuasa $suratKuasa)
    {
        // Nomor surat
        $nomor_surat = 'SK/' . str_pad($suratKuasa->id, 4, '0', STR_PAD_LEFT) . '/' . date('m/Y');

        // Set locale dan ambil komponen tanggal
        $created = Carbon::parse($suratKuasa->created_at)->locale('id');
        Carbon::setLocale('id');

        $hari_pembuatan = $created->translatedFormat('l');
        $tanggal = $created->translatedFormat('d');
        $bulan = $created->translatedFormat('F');
        $tahun = $created->translatedFormat('Y');
        $tanggal_pembuatan = $created->translatedFormat('d F Y');

        // Generate QR Code
        $qrCodeData = [
            'id'                => $suratKuasa->id,
            'nomor_surat'       => $nomor_surat,
            'nama_pemberi'      => $suratKuasa->nama_pemberi,
            'nama_penerima'     => $suratKuasa->nama_penerima,
            'tanggal_pembuatan' => $tanggal_pembuatan,
            'verification_url'  => url('/verify-surat/' . $suratKuasa->id),
            'hash'              => hash('sha256', $suratKuasa->id . $suratKuasa->nama_pemberi . $suratKuasa->created_at)
        ];

        $qrcode = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                  ->size(300)
                  ->margin(2)
                  ->errorCorrection('H')
                  ->generate(json_encode($qrCodeData))
        );

        // Generate barcode
        $generator = new BarcodeGeneratorPNG();
        $barcodePng = $generator->getBarcode($nomor_surat, $generator::TYPE_CODE_128);
        $barcode = 'data:image/png;base64,' . base64_encode($barcodePng);

        // Data untuk view
        $data = [
            'surat_kuasa'       => $suratKuasa,
            'nomor_surat'       => $nomor_surat,
            'tanggal_pembuatan' => $tanggal_pembuatan,
            'hari_pembuatan'    => $hari_pembuatan,
            'tanggal'           => $tanggal,
            'bulan'             => $bulan,
            'tahun'             => $tahun,
            'qrcode'            => $qrcode,
            'barcode'           => $barcode,
        ];

        // PASTIKAN MENGGUNAKAN VIEW F-1.07
        $pdf = PDF::loadView('pdf.surat-kuasa', $data)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont'           => 'Times New Roman',
                'isHtml5ParserEnabled'  => true,
                'isPhpEnabled'          => true,
            ]);

        // Simpan PDF
        $fileName = 'surat-kuasa-' . $suratKuasa->id . '-' . time() . '.pdf';
        $filePath = 'pdf/' . $fileName;
        Storage::disk('public')->put($filePath, $pdf->output());

        // Update record
        $suratKuasa->update([
            'pdf_file' => $filePath
        ]);

        return $filePath;
    }
}
