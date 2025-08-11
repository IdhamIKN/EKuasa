<!-- resources/views/surat-kuasa/success.blade.php -->
@extends('layouts.app')
@section('title', 'Pengajuan Berhasil - Surat Kuasa Online')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg mt-5">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="text-success mb-3">Pengajuan Berhasil Dikirim!</h2>

                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Pengajuan
                        </h5>
                        <hr>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <strong>ID Pengajuan:</strong><br>
                                <code class="fs-6">{{ $suratKuasa->id }}</code>
                            </div>
                            <div class="col-md-6">
                                <strong>Tanggal Pengajuan:</strong><br>
                                {{ $suratKuasa->tanggal_pengajuan_formatted }}
                            </div>
                        </div>
                        <div class="row mt-3 text-start">
                            <div class="col-md-6">
                                <strong>Nama Pemberi Kuasa:</strong><br>
                                {{ $suratKuasa->nama_pemberi }}
                            </div>
                            <div class="col-md-6">
                                <strong>Nama Penerima Kuasa:</strong><br>
                                {{ $suratKuasa->nama_penerima }}
                            </div>
                        </div>
                        <div class="row mt-3 text-start">
                            <div class="col-12">
                                <strong>Status:</strong><br>
                                <span class="badge bg-warning">{{ $suratKuasa->status_label }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <h6 class="mb-3">
                            <i class="fas fa-whatsapp me-2"></i>
                            Notifikasi WhatsApp Telah Dikirim
                        </h6>
                        <p class="mb-0">
                            Notifikasi konfirmasi telah dikirimkan ke nomor WhatsApp <strong>{{ $suratKuasa->no_hp_pemberi }}</strong>.
                            Silakan cek pesan masuk Anda.
                        </p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Penting untuk Diingat
                                    </h6>
                                    <ul class="list-unstyled text-start small">
                                        <li><i class="fas fa-save text-primary me-2"></i>Simpan ID pengajuan untuk melacak status</li>
                                        <li><i class="fas fa-mobile-alt text-primary me-2"></i>Pastikan HP aktif untuk menerima notifikasi</li>
                                        <li><i class="fas fa-eye text-primary me-2"></i>Cek WhatsApp secara berkala</li>
                                        <li><i class="fas fa-redo text-primary me-2"></i>Jika ditolak, Anda bisa mengajukan ulang</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-clock me-2"></i>
                                        Proses Selanjutnya
                                    </h6>
                                    <ul class="list-unstyled text-start small">
                                        <li><i class="fas fa-check text-success me-2"></i>Admin akan memverifikasi data Anda</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Proses verifikasi maksimal 1x24 jam</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Notifikasi hasil akan dikirim via WhatsApp</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Jika disetujui, link download PDF akan dikirimkan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Process -->
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-list-alt me-2"></i>
                                Alur Proses Surat Kuasa
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item completed">
                                    <div class="timeline-marker bg-success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Pengajuan Dikirim</h6>
                                        <p class="text-muted small mb-0">Data Anda telah berhasil dikirim ke sistem</p>
                                    </div>
                                </div>
                                <div class="timeline-item active">
                                    <div class="timeline-marker bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Verifikasi Admin</h6>
                                        <p class="text-muted small mb-0">Admin sedang memverifikasi dokumen dan data Anda</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-secondary">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Pembuatan PDF</h6>
                                        <p class="text-muted small mb-0">Surat kuasa akan dibuat dalam format PDF</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-secondary">
                                        <i class="fas fa-download"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Siap Diunduh</h6>
                                        <p class="text-muted small mb-0">Link download akan dikirim via WhatsApp</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="alert alert-light border mt-4">
                        <h6 class="alert-heading">
                            <i class="fas fa-phone-alt me-2"></i>
                            Butuh Bantuan?
                        </h6>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <strong>WhatsApp Admin:</strong><br>
                                <a href="https://wa.me/6281234567890" class="text-success">
                                    <i class="fab fa-whatsapp me-1"></i>
                                    +62 812-3456-7890
                                </a>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong><br>
                                <a href="mailto:admin@suratkuasa.com" class="text-primary">
                                    <i class="fas fa-envelope me-1"></i>
                                    admin@suratkuasa.com
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('surat-kuasa.track-form') }}" class="btn btn-primary me-3">
                            <i class="fas fa-search me-2"></i>
                            Lacak Status Pengajuan
                        </a>
                        <a href="{{ route('surat-kuasa.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            Ajukan Surat Kuasa Lain
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 30px;
    height: 100%;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 70px;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-item.completed .timeline-marker {
    background-color: #28a745;
}

.timeline-item.active .timeline-marker {
    background-color: #ffc107;
    animation: pulse 2s infinite;
}

.timeline-item:not(.completed):not(.active) .timeline-marker {
    background-color: #6c757d;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
    }
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #495057;
}

.timeline-content p {
    margin-bottom: 0;
    font-size: 0.9rem;
}
</style>
@endsection
