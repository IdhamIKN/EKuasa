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

    // public function generatePDF($id)
    // {
    //     // Pastikan hanya admin yang bisa
    //     if ($redirect = $this->checkAdminRole()) {
    //         return $redirect;
    //     }

    //     try {
    //         $suratKuasa = SuratKuasa::findOrFail($id);

    //         $pdfService = new PDFService();
    //         $filePath = $pdfService->generateSuratKuasaPDF($suratKuasa);

    //         // Update status
    //         $suratKuasa->update([
    //             'status' => SuratKuasa::STATUS_DISETUJUI
    //         ]);

    //         return response()->json([
    //             'success'      => true,
    //             'message'      => 'PDF berhasil dibuat',
    //             'download_url' => route('admin.surat-kuasa.download', $id)
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('PDF generation failed: ' . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal generate PDF: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function generatePDF($id)
    {
        if ($redirect = $this->checkAdminRole()) {
            return $redirect;
        }

        try {
            $suratKuasa = SuratKuasa::findOrFail($id);

            // Format nama
            $suratKuasa->nama_penerima = $this->shortenNameWithInitialsRobust($suratKuasa->nama_penerima);
            $suratKuasa->nama_pemberi = $this->shortenNameWithInitialsRobust($suratKuasa->nama_pemberi);
            // dd( $suratKuasa->nama_penerima, $suratKuasa->nama_pemberi);
            // Data untuk QR code
            $qrData = [
                'id' => $suratKuasa->id,
                'pemberi' => $suratKuasa->nama_pemberi,
                'penerima' => $suratKuasa->nama_penerima,
                'tanggal' => now()->format('Y-m-d'),
                'hash' => substr(md5($suratKuasa->id . config('app.key')), 0, 12)
            ];

            // Generate QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate(json_encode($qrData));

            $qrCodeBase64 = base64_encode($qrCode);

            // Generate PDF dengan menyertakan QR code
            $pdfService = new PDFService();
            $filePath = $pdfService->generateSuratKuasaPDF($suratKuasa, $qrCodeBase64);

            // Update status
            $suratKuasa->update(['status' => SuratKuasa::STATUS_DISETUJUI]);

            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat',
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
    private function shortenNameWithInitialsRobust($name)
    {
        // Validasi input
        if (!is_string($name) || empty(trim($name))) {
            return $name;
        }

        $name = trim(preg_replace('/\s+/', ' ', $name)); // Normalize multiple spaces

        // Jika nama tidak lebih dari 20 karakter, return as is
        if (strlen($name) <= 20) {
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

    /**
     * Fungsi generatePDF yang sudah diperbaiki
     */



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
