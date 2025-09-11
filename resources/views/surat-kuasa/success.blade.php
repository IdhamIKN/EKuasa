<!-- resources/views/surat-kuasa/success.blade.php -->
@extends('layouts.app')
@section('title', 'Pengajuan Berhasil - Surat Kuasa Online')
@section('content')
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

    .copy-container {
        position: relative;
        display: inline-block;
    }

    .copy-btn {
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 2px 5px;
        margin-left: 8px;
        border-radius: 3px;
        transition: all 0.2s ease;
    }

    .copy-btn:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    .copy-btn.copied {
        color: #28a745;
    }

    .id-display {
        display: inline-flex;
        align-items: center;
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        user-select: all;
    }

    .copy-tooltip {
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
        z-index: 1000;
    }

    .copy-tooltip.show {
        opacity: 1;
    }

    .copy-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #333;
    }

    /* Demo styling */
    .demo-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
    }

    .info-card {
        background: #e8f4fd;
        border: 1px solid #b6e2ff;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .success-badge {
        background: #28a745;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        margin-bottom: 1rem;
        display: inline-block;
    }

</style>
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
                            <!-- Tracking Number Display -->
                            <div class="alert alert-info border-0 mb-1">
                                <h5 class="alert-heading">
                                    <i class="fas fa-barcode me-2"></i>
                                    Nomor Tracking Anda
                                </h5>
                                <h3 class="mb-1" style="font-family: monospace; letter-spacing: 2px; color: #0066cc;">
                                    {{ session('tracking_number') ?? $suratKuasa->tracking_number }}
                                </h3>
                                <p class="mb-0">
                                    <strong>Penting!</strong> Simpan nomor tracking ini untuk melacak status pengajuan Anda.
                                </p>
                            </div>

                            <!-- Copy Button -->
                            <div class="mb-2">
                                <button type="button" id="copyBtn" class="btn btn-outline-primary" onclick="copyTrackingNumber(event)">
                                    <i class="fas fa-copy me-2"></i>
                                    Salin Nomor Tracking
                                </button>
                                <small id="copySuccess" class="text-success ms-2" style="display:none;">
                                    <i class="fas fa-check-circle"></i> Berhasil disalin!
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <strong>ID Pengajuan:</strong><br>
                                <div class="copy-container mt-2">
                                    <div class="id-display">
                                        <span id="pengajuan-id">{{ $suratKuasa->id }}</span>
                                        {{-- Aktifkan untuk copy berupa ID saja --}}
                                        {{-- <button class="copy-btn" onclick="copyToClipboard('pengajuan-id', this)" title="Copy ID">
                                            <i class="fas fa-copy"></i>
                                        </button> --}}
                                    </div>
                                    {{-- <div class="copy-tooltip">ID berhasil di-copy!</div> --}}
                                </div>
                                {{-- <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Gunakan ID ini untuk melacak pengajuan Anda
                                </small> --}}
                            </div>
                            <!-- Elemen NIK tersembunyi -->
                            <div id="nik-data" data-nik="{{ $suratKuasa->nik_pemberi }}" style="display:none;"></div>
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

                    {{-- <div class="alert alert-success">
                        <h6 class="mb-3">
                            <i class="fas fa-whatsapp me-2"></i>
                            Notifikasi WhatsApp Telah Dikirim
                        </h6>
                        <p class="mb-0">
                            Notifikasi konfirmasi telah dikirimkan ke nomor WhatsApp <strong>{{ $suratKuasa->no_hp_pemberi }}</strong>.
                    Silakan cek pesan masuk Anda.
                    </p>
                </div> --}}

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
                                    {{-- <li><i class="fas fa-mobile-alt text-primary me-2"></i>Pastikan HP aktif untuk menerima notifikasi</li>
                                    <li><i class="fas fa-eye text-primary me-2"></i>Cek WhatsApp secara berkala</li> --}}
                                    <li><i class="fas fa-clock text-primary me-2"></i>Lacak pengajuan secara berkala</li>
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
                                    {{-- <li><i class="fas fa-check text-success me-2"></i>Notifikasi hasil akan dikirim via WhatsApp</li> --}}
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
                                    {{-- <p class="text-muted small mb-0">Link download akan dikirim via WhatsApp</p> --}}
                                    <p class="text-muted small mb-0">Surat Kuasa siap diunduh</p>
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
                                +62 896-1861-9880
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

<script>
    function copyTrackingNumber(event) {
        const trackingNumber = "{{ session('tracking_number') ?? $suratKuasa->tracking_number }}";
        const btn = event.target.closest("button");
        const successMsg = document.getElementById("copySuccess");

        if (navigator.clipboard) {
            navigator.clipboard.writeText(trackingNumber).then(function() {
                // Ubah tombol sementara
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Tersalin!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-outline-primary');

                // Tampilkan pesan sukses
                successMsg.style.display = "inline";

                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                    successMsg.style.display = "none";
                }, 2000);
            });
        } else {
            alert('Nomor tracking telah disalin: ' + trackingNumber);
        }
    }

</script>

<!-- 4. JAVASCRIPT untuk format input tracking number -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trackingInput = document.getElementById('tracking_number');
        const nikInput = document.getElementById('nik');

        if (trackingInput) {
            trackingInput.addEventListener('input', function(e) {
                // Convert to uppercase
                e.target.value = e.target.value.toUpperCase();

                // Remove non-alphanumeric characters
                e.target.value = e.target.value.replace(/[^A-Z0-9]/g, '');
            });
        }

        if (nikInput) {
            nikInput.addEventListener('input', function(e) {
                // Only allow numbers
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }
    });

</script>
@endsection
