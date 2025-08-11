<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\SuratKuasa;
use App\Services\WhatsAppService;
use App\Services\PDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AdminController extends Controller
{
    protected $whatsappService;
    protected $pdfService;

    public function __construct(WhatsAppService $whatsappService, PDFService $pdfService)
    {
        $this->middleware('auth');
        $this->whatsappService = $whatsappService;
        $this->pdfService = $pdfService;
    }

    /**
     * Check if current user is admin
     */
    private function checkAdminRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.',
            ]);
        }
        return null;
    }

    public function dashboard(Request $request)
    {
        // Check admin role
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) return $roleCheck;

        $query = SuratKuasa::query();

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemberi', 'like', "%{$search}%")
                    ->orWhere('nik_pemberi', 'like', "%{$search}%")
                    ->orWhere('nama_penerima', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $suratKuasas = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistics
        $stats = [
            'total' => SuratKuasa::count(),
            'pending' => SuratKuasa::pending()->count(),
            'disetujui' => SuratKuasa::disetujui()->count(),
            'ditolak' => SuratKuasa::ditolak()->count(),
        ];

        return view('admin.dashboard', compact('suratKuasas', 'stats'));
    }

    public function show($id)
    {
        // Check admin role
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) return $roleCheck;

        $suratKuasa = SuratKuasa::findOrFail($id);
        return view('admin.detail', compact('suratKuasa'));
    }

    public function approve(Request $request, $id)
    {
        // Check admin role
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) return $roleCheck;

        try {
            $suratKuasa = SuratKuasa::findOrFail($id);

            if ($suratKuasa->status !== SuratKuasa::STATUS_PENDING) {
                return back()->withErrors(['error' => 'Hanya pengajuan dengan status pending yang dapat disetujui.']);
            }

            // Update status
            $suratKuasa->update([
                'status' => SuratKuasa::STATUS_DISETUJUI,
                'alasan_penolakan' => null
            ]);

            // Generate PDF
            $this->pdfService->generateSuratKuasaPDF($suratKuasa);

            // Send WhatsApp notification
            $this->whatsappService->sendApprovedNotification($suratKuasa);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Surat kuasa berhasil disetujui dan PDF telah digenerate!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, $id)
    {
        // Check admin role
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) return $roleCheck;

        $request->validate([
            'alasan_penolakan' => 'required|string|min:10'
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter'
        ]);

        try {
            $suratKuasa = SuratKuasa::findOrFail($id);

            if ($suratKuasa->status !== SuratKuasa::STATUS_PENDING) {
                return back()->withErrors(['error' => 'Hanya pengajuan dengan status pending yang dapat ditolak.']);
            }

            // Update status
            $suratKuasa->update([
                'status' => SuratKuasa::STATUS_DITOLAK,
                'alasan_penolakan' => $request->alasan_penolakan
            ]);

            // Send WhatsApp notification
            $this->whatsappService->sendRejectedNotification($suratKuasa);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Surat kuasa berhasil ditolak dan notifikasi telah dikirim!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function downloadPDF($id)
    {
        // Check admin role
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) return $roleCheck;

        $suratKuasa = SuratKuasa::findOrFail($id);

        if (!$suratKuasa->pdf_file || $suratKuasa->status !== SuratKuasa::STATUS_DISETUJUI) {
            return back()->withErrors(['error' => 'PDF tidak tersedia atau surat kuasa belum disetujui.']);
        }

        $filePath = storage_path('app/public/' . $suratKuasa->pdf_file);

        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File PDF tidak ditemukan.']);
        }

        return response()->download($filePath, 'Surat_Kuasa_' . $suratKuasa->nama_pemberi . '.pdf');
    }

    // Method untuk generate PDF (update method yang sudah ada)
    // public function generatePDF($id)
    // {
    //     // Check admin role
    //     $roleCheck = $this->checkAdminRole();
    //     if ($roleCheck) return $roleCheck;

    //     $suratKuasa = SuratKuasa::findOrFail($id);

    //     // Generate nomor surat
    //     $nomor_surat = 'SK/' . str_pad($suratKuasa->id, 4, '0', STR_PAD_LEFT) . '/' . date('m/Y');

    //     // Format tanggal pembuatan
    //     $tanggal_pembuatan = Carbon::parse($suratKuasa->created_at)->translatedFormat('d F Y');

    //     // Generate QR Code
    //     $qrCodeData = json_encode([
    //         'id' => $suratKuasa->id,
    //         'nomor_surat' => $nomor_surat,
    //         'nama_pemberi' => $suratKuasa->nama_pemberi,
    //         'nama_penerima' => $suratKuasa->nama_penerima,
    //         'tanggal_pembuatan' => $tanggal_pembuatan,
    //         'verification_url' => url('/verify-surat/' . $suratKuasa->id),
    //         'hash' => hash('sha256', $suratKuasa->id . $suratKuasa->nama_pemberi . $suratKuasa->created_at)
    //     ]);

    //     // Generate QR Code sebagai base64
    //     $qrcode = base64_encode(QrCode::format('png')
    //         ->size(300)
    //         ->margin(2)
    //         ->errorCorrection('H')
    //         ->generate($qrCodeData));

    //     $qrcode = 'data:image/png;base64,' . $qrcode;

    //     $data = [
    //         'surat_kuasa' => $suratKuasa,
    //         'nomor_surat' => $nomor_surat,
    //         'tanggal_pembuatan' => $tanggal_pembuatan,
    //         'qrcode' => $qrcode
    //     ];

    //     // Generate PDF menggunakan library seperti DomPDF atau wkhtmltopdf
    //     $pdf = PDF::loadView('pdf.surat-kuasa', $data)
    //         ->setPaper('A4', 'portrait')
    //         ->setOptions([
    //             'defaultFont' => 'Inter',
    //             'isHtml5ParserEnabled' => true,
    //             'isPhpEnabled' => true,
    //             'debugPng' => false,
    //             'debugKeepTemp' => false,
    //             'debugCss' => false,
    //             'margin_top' => 10,
    //             'margin_right' => 10,
    //             'margin_bottom' => 10,
    //             'margin_left' => 10,
    //         ]);

    //     // Simpan PDF
    //     $fileName = 'surat-kuasa-' . $suratKuasa->id . '-' . time() . '.pdf';
    //     $filePath = 'pdf/' . $fileName;

    //     Storage::disk('public')->put($filePath, $pdf->output());

    //     // Update record dengan path PDF
    //     $suratKuasa->update([
    //         'pdf_file' => $filePath,
    //         'status' => SuratKuasa::STATUS_DISETUJUI
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'PDF berhasil dibuat',
    //         'download_url' => route('admin.surat-kuasa.download', $id)
    //     ]);
    // }
  public function generatePDF($id)
    {
        // Pastikan hanya admin yang bisa
        if ($redirect = $this->checkAdminRole()) {
            return $redirect;
        }

        $suratKuasa = SuratKuasa::findOrFail($id);

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

        // Data untuk QR code
        $qrCodeData = [
            'id'                => $suratKuasa->id,
            'nomor_surat'       => $nomor_surat,
            'nama_pemberi'      => $suratKuasa->nama_pemberi,
            'nama_penerima'     => $suratKuasa->nama_penerima,
            'tanggal_pembuatan' => $tanggal_pembuatan,
            'verification_url'  => url('/verify-surat/' . $suratKuasa->id),
            'hash'              => hash('sha256', $suratKuasa->id . $suratKuasa->nama_pemberi . $suratKuasa->created_at)
        ];

        // Generate QR Code dengan error handling
        $qrcode = null;
        try {
            Log::info('Attempting to generate QR code...');

            $qrImageData = QrCode::format('png')
                ->size(300)
                ->margin(2)
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

        // Generate barcode dengan error handling
        $barcode = null;
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcodePng = $generator->getBarcode($nomor_surat, $generator::TYPE_CODE_128);
            $barcode = 'data:image/png;base64,' . base64_encode($barcodePng);

            Log::info('Barcode generated successfully');

        } catch (\Exception $e) {
            Log::error('Barcode generation failed: ' . $e->getMessage());
            $barcode = null;
        }

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

        try {
            // Generate PDF
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
                'pdf_file' => $filePath,
                'status'   => SuratKuasa::STATUS_DISETUJUI
            ]);

            Log::info('PDF generated successfully: ' . $filePath);

            return response()->json([
                'success'      => true,
                'message'      => 'PDF berhasil dibuat',
                'download_url' => route('admin.surat-kuasa.download', $id)
            ]);

        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method tambahan untuk verifikasi QR Code
    public function verifySurat($id)
    {
        $suratKuasa = SuratKuasa::find($id);

        if (!$suratKuasa) {
            return response()->json([
                'valid' => false,
                'message' => 'Surat kuasa tidak ditemukan'
            ], 404);
        }

        $verification_hash = hash('sha256', $suratKuasa->id . $suratKuasa->nama_pemberi . $suratKuasa->created_at);

        return response()->json([
            'valid' => true,
            'data' => [
                'id' => $suratKuasa->id,
                'nomor_surat' => 'SK/' . str_pad($suratKuasa->id, 4, '0', STR_PAD_LEFT) . '/' . date('m/Y', strtotime($suratKuasa->created_at)),
                'nama_pemberi' => $suratKuasa->nama_pemberi,
                'nama_penerima' => $suratKuasa->nama_penerima,
                'tanggal_pembuatan' => Carbon::parse($suratKuasa->created_at)->translatedFormat('d F Y'),
                'status' => $suratKuasa->status,
                'hash' => $verification_hash
            ]
        ]);
    }

    // Method untuk preview PDF sebelum download
    public function previewPDF($id)
    {
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) return $roleCheck;

        $suratKuasa = SuratKuasa::findOrFail($id);

        if (!$suratKuasa->pdf_file || $suratKuasa->status !== SuratKuasa::STATUS_DISETUJUI) {
            return back()->withErrors(['error' => 'PDF tidak tersedia atau surat kuasa belum disetujui.']);
        }

        $filePath = storage_path('app/public/' . $suratKuasa->pdf_file);

        if (!file_exists($filePath)) {
            return back()->withErrors(['error' => 'File PDF tidak ditemukan.']);
        }

        return response()->file($filePath);
    }
}
