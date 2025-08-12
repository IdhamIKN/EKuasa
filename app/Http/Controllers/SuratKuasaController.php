<?php
// app/Http/Controllers/SuratKuasaController.php

namespace App\Http\Controllers;

use App\Models\SuratKuasa;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SuratKuasaController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function create()
    {
        return view('surat-kuasa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik_pemberi' => 'required|string|size:16',
            'nama_pemberi' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:' . now()->subYears(17)->format('Y-m-d'),
            'usia_pemberi' => 'required|integer|min:17|max:100',
            'pekerjaan_pemberi' => 'required|string|max:255',
            'alamat_pemberi' => 'required|string',
            'nama_penerima' => 'required|string|max:255',
            'nik_penerima' => 'required|string|size:16',
            'alasan' => 'required|string',
            'foto_pemberi_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'no_hp_pemberi' => 'required|string|min:10|max:15',
            'kota_pengajuan' => 'required|string|max:255'
        ], [
            'nik_pemberi.required' => 'NIK Pemberi Kuasa wajib diisi',
            'nik_pemberi.size' => 'NIK harus 16 digit',
            'nama_pemberi.required' => 'Nama Pemberi Kuasa wajib diisi',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'tanggal_lahir.before_or_equal' => 'Umur minimal 17 tahun',
            'usia_pemberi.required' => 'Usia wajib diisi',
            'usia_pemberi.min' => 'Usia minimal 17 tahun',
            'usia_pemberi.max' => 'Usia maksimal 100 tahun',
            'pekerjaan_pemberi.required' => 'Pekerjaan wajib diisi',
            'alamat_pemberi.required' => 'Alamat wajib diisi',
            'nama_penerima.required' => 'Nama Penerima Kuasa wajib diisi',
            'nik_penerima.required' => 'NIK Penerima Kuasa wajib diisi',
            'nik_penerima.size' => 'NIK harus 16 digit',
            'alasan.required' => 'Alasan pemberian kuasa wajib diisi',
            'foto_pemberi_ktp.required' => 'Foto KTP wajib diupload',
            'foto_pemberi_ktp.image' => 'File harus berupa gambar',
            'foto_pemberi_ktp.mimes' => 'Format gambar harus JPEG, PNG, atau JPG',
            'foto_pemberi_ktp.max' => 'Ukuran gambar maksimal 2MB',
            'no_hp_pemberi.required' => 'Nomor HP wajib diisi',
            'kota_pengajuan.required' => 'Kota pengajuan wajib diisi'
        ]);

        try {
            // Combine tempat_lahir and tanggal_lahir into ttl_pemberi
            $tanggalLahir = Carbon::parse($request->tanggal_lahir);
            $ttlPemberi = $request->tempat_lahir . ', ' . $tanggalLahir->locale('id')->translatedFormat('d F Y');

            // Double-check age calculation (optional security measure)
            // $calculatedAge = $tanggalLahir->diffInYears(now());
            // if ($calculatedAge != $request->usia_pemberi) {
            //     return back()->withInput()
            //         ->withErrors(['usia_pemberi' => 'Usia tidak sesuai dengan tanggal lahir yang dipilih']);
            // }

            // Handle file upload
            if ($request->hasFile('foto_pemberi_ktp')) {
                $file = $request->file('foto_pemberi_ktp');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('ktp-photos', $fileName, 'public');
            }

            // Create surat kuasa record
            $suratKuasa = SuratKuasa::create([
                'nik_pemberi' => $request->nik_pemberi,
                'tanggal_pengajuan' => now()->toDateString(),
                'kota_pengajuan' => $request->kota_pengajuan,
                'nama_pemberi' => $request->nama_pemberi,
                'ttl_pemberi' => $ttlPemberi, // Combined tempat + tanggal lahir
                'usia_pemberi' => $request->usia_pemberi,
                'pekerjaan_pemberi' => $request->pekerjaan_pemberi,
                'alamat_pemberi' => $request->alamat_pemberi,
                'nama_penerima' => $request->nama_penerima,
                'nik_penerima' => $request->nik_penerima,
                'alasan' => $request->alasan,
                'foto_pemberi_ktp' => $filePath ?? null,
                'no_hp_pemberi' => $request->no_hp_pemberi,
                'status' => SuratKuasa::STATUS_PENDING
            ]);

            // Send WhatsApp notification
            $this->whatsappService->sendPendingNotification($suratKuasa);

            return redirect()->route('surat-kuasa.success', $suratKuasa->id)
                ->with('success', 'Pengajuan surat kuasa berhasil dikirim! Anda akan mendapat notifikasi melalui WhatsApp.');
        } catch (\Exception $e) {
            // Delete uploaded file if exists
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function success($id)
    {
        $suratKuasa = SuratKuasa::findOrFail($id);
        return view('surat-kuasa.success', compact('suratKuasa'));
    }

    public function track(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'nik' => 'required|string|size:16'
        ]);

        $suratKuasa = SuratKuasa::where('id', $request->id)
            ->where('nik_pemberi', $request->nik)
            ->first();

        if (!$suratKuasa) {
            return back()->withErrors(['error' => 'Data tidak ditemukan. Periksa kembali ID dan NIK Anda.']);
        }

        return view('surat-kuasa.track', compact('suratKuasa'));
    }

    public function showTrackForm()
    {
        return view('surat-kuasa.track-form');
    }
}
