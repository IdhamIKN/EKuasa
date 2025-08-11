<?php
// app/Services/WhatsAppService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->apiKey = config('services.whatsapp.api_key');
    }

    public function sendMessage($phoneNumber, $message)
    {
        try {
            // Format phone number (remove leading 0 and add 62)
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'receiver' => $formattedPhone,
                'data' => [
                    'message' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $formattedPhone,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $formattedPhone,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage(), [
                'phone' => $phoneNumber,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function sendPendingNotification($suratKuasa)
    {
        $message = "🔔 *SURAT KUASA ONLINE*\n\n";
        $message .= "Halo {$suratKuasa->nama_pemberi},\n\n";
        $message .= "Pengajuan surat kuasa Anda telah diterima dengan detail:\n";
        $message .= "• ID Pengajuan: {$suratKuasa->id}\n";
        $message .= "• Tanggal: {$suratKuasa->tanggal_pengajuan_formatted}\n";
        $message .= "• Status: *Menunggu Verifikasi*\n\n";
        $message .= "Mohon tunggu proses verifikasi dari admin. Anda akan mendapat notifikasi lebih lanjut.\n\n";
        $message .= "Terima kasih.";

        return $this->sendMessage($suratKuasa->no_hp_pemberi, $message);
    }

    public function sendApprovedNotification($suratKuasa)
    {
        $message = "✅ *SURAT KUASA DISETUJUI*\n\n";
        $message .= "Halo {$suratKuasa->nama_pemberi},\n\n";
        $message .= "Selamat! Pengajuan surat kuasa Anda telah *DISETUJUI*.\n\n";
        $message .= "Detail pengajuan:\n";
        $message .= "• ID: {$suratKuasa->id}\n";
        $message .= "• Penerima Kuasa: {$suratKuasa->nama_penerima}\n";
        $message .= "• Tanggal Disetujui: " . now()->format('d F Y H:i') . "\n\n";

        if ($suratKuasa->pdf_file) {
            $pdfUrl = asset('storage/' . $suratKuasa->pdf_file);
            $message .= "📄 *Download Surat Kuasa Anda:*\n";
            $message .= "{$pdfUrl}\n\n";
        }

        $message .= "Surat kuasa ini telah dilengkapi dengan barcode sebagai tanda sah.\n\n";
        $message .= "Terima kasih telah menggunakan layanan kami.";

        return $this->sendMessage($suratKuasa->no_hp_pemberi, $message);
    }

    public function sendRejectedNotification($suratKuasa)
    {
        $message = "❌ *SURAT KUASA DITOLAK*\n\n";
        $message .= "Halo {$suratKuasa->nama_pemberi},\n\n";
        $message .= "Mohon maaf, pengajuan surat kuasa Anda telah *DITOLAK*.\n\n";
        $message .= "Alasan penolakan:\n";
        $message .= "{$suratKuasa->alasan_penolakan}\n\n";
        $message .= "Anda dapat mengajukan kembali dengan memperbaiki data sesuai alasan penolakan di atas.\n\n";
        $message .= "Link pengajuan: " . route('surat-kuasa.create') . "\n\n";
        $message .= "Terima kasih atas pengertian Anda.";

        return $this->sendMessage($suratKuasa->no_hp_pemberi, $message);
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, add it
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
