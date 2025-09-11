<?php
// app/Models/SuratKuasa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Carbon\Carbon;

class SuratKuasa extends Model
{
    use HasUuids;
    protected $table = 'surat_kuasa';
    protected $fillable = [
        'tracking_number', // Tambahkan field baru
        'nik_pemberi',
        'tanggal_pengajuan',
        'kota_pengajuan',
        'nama_pemberi',
        'ttl_pemberi',
        'usia_pemberi',
        'pekerjaan_pemberi',
        'alamat_pemberi',
        'nama_penerima',
        'nik_penerima',
        'alasan',
        'foto_pemberi_ktp',
        'no_hp_pemberi',
        'status',
        'alasan_penolakan',
        'pdf_file'
    ];

    protected $dates = [
        'tanggal_pengajuan',
        'created_at',
        'updated_at'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';

    // Method untuk generate tracking number
    public static function generateTrackingNumber()
    {
        $today = now();
        $datePrefix = $today->format('dmY'); // Format: ddmmyyyy (misalkan hari ini 10092025)

        // Generate 3 huruf acak
        $letters = '';
        for ($i = 0; $i < 3; $i++) {
            $letters .= chr(rand(65, 90)); // A-Z
        }

        // Cari nomor urut terakhir hari ini
        $lastNumber = self::whereDate('created_at', $today->toDateString())
            ->whereNotNull('tracking_number')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastNumber && $lastNumber->tracking_number) {
            // Ambil 4 digit terakhir dari tracking number
            $lastSequence = (int) substr($lastNumber->tracking_number, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        // Format: ABC10092025001
        return $letters . $datePrefix . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }

    // Method untuk mencari berdasarkan tracking number
    public static function findByTrackingNumber($trackingNumber, $nik = null)
    {
        $query = self::where('tracking_number', strtoupper($trackingNumber));

        if ($nik) {
            $query->where('nik_pemberi', $nik);
        }

        return $query->first();
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_DISETUJUI => 'success',
            self::STATUS_DITOLAK => 'danger',
            default => 'secondary'
        };
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_pemberi_ktp) {
            return asset('storage/' . $this->foto_pemberi_ktp);
        }
        return null;
    }

    public function getPdfUrlAttribute()
    {
        if ($this->pdf_file) {
            return asset('storage/' . $this->pdf_file);
        }
        return null;
    }

    public function getTanggalPengajuanFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_pengajuan)->format('d F Y');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', self::STATUS_DITOLAK);
    }
}
