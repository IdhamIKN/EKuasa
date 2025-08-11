<!-- resources/views/surat-kuasa/track.blade.php -->
@extends('layouts.app')

@section('title', 'Status Pengajuan - Surat Kuasa Online')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg mt-4">
                <div class="card-body p-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <i class="fas fa-file-contract fa-3x text-primary mb-3"></i>
                        <h2>Status Pengajuan Surat Kuasa</h2>
                        <p class="text-muted">ID Pengajuan: <code>{{ $suratKuasa->id }}</code></p>
                    </div>

                    <!-- Status Badge -->
                    <div class="text-center mb-4">
                        @if($suratKuasa->status === 'pending')
                            <span class="badge bg-warning px-4 py-2 fs-5">
                                <i class="fas fa-clock me-2"></i>
                                {{ $suratKuasa->status_label }}
                            </span>
                        @elseif($suratKuasa->status === 'disetujui')
                            <span class="badge bg-success px-4 py-2 fs-5">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ $suratKuasa->status_label }}
                            </span>
                        @elseif($suratKuasa->status === 'ditolak')
                            <span class="badge bg-danger px-4 py-2 fs-5">
                                <i class="fas fa-times-circle me-2"></i>
                                {{ $suratKuasa->status_label }}
                            </span>
                        @endif
                    </div>

                    <!-- Timeline -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="timeline-horizontal">
                                <div class="timeline-step {{ $suratKuasa->status !== 'pending' ? 'completed' : 'active' }}">
                                    <div class="timeline-step-icon">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <div class="timeline-step-content">
                                        <h6>Pengajuan Dikirim</h6>
                                    </div>
                                </div>
                                <div class="timeline-step {{ $suratKuasa->status === 'disetujui' ? 'completed' : ($suratKuasa->status === 'ditolak' ? 'rejected' : '') }}">
                                    <div class="timeline-step-icon">
                                        @if($suratKuasa->status === 'disetujui')
                                            <i class="fas fa-check"></i>
                                        @elseif($suratKuasa->status === 'ditolak')
                                            <i class="fas fa-times"></i>
                                        @else
                                            <i class="fas fa-hourglass-half"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-step-content">
                                        <h6>Verifikasi Admin</h6>
                                        @if($suratKuasa->status !== 'pending')
                                            <small>{{ $suratKuasa->updated_at->format('d/m/Y H:i') }}</small>
                                        @else
                                            <small>Sedang diproses</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="timeline-step {{ $suratKuasa->status === 'disetujui' ? 'completed' : '' }}">
                                    <div class="timeline-step-icon">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    <div class="timeline-step-content">
                                        <h6>PDF Selesai</h6>
                                        @if($suratKuasa->status === 'disetujui')
                                            <small>Siap diunduh</small>
                                        @else
                                            <small>Menunggu persetujuan</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-user me-2"></i>
                                        Data Pemberi Kuasa
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%">Nama:</td>
                                            <td><strong>{{ $suratKuasa->nama_pemberi }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>NIK:</td>
                                            <td>{{ $suratKuasa->nik_pemberi }}</td>
                                        </tr>
                                        <tr>
                                            <td>Usia:</td>
                                            <td>{{ $suratKuasa->usia_pemberi }} tahun</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-user-tie me-2"></i>
                                        Data Penerima Kuasa
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td width="40%">Nama:</td>
                                            <td><strong>{{ $suratKuasa->nama_penerima }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>NIK:</td>
                                            <td>{{ $suratKuasa->nik_penerima }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal:</td>
                                            <td>{{ $suratKuasa->tanggal_pengajuan_formatted }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status-specific content -->
                    @if($suratKuasa->status === 'pending')
                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Sedang Diproses
                            </h6>
                            <p class="mb-0">Pengajuan Anda sedang dalam tahap verifikasi oleh admin. Mohon tunggu maksimal 1x24 jam. Anda akan mendapat notifikasi WhatsApp ketika status berubah.</p>
                        </div>
                    @elseif($suratKuasa->status === 'disetujui')
                        <div class="alert alert-success mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>
                                Pengajuan Disetujui!
                            </h6>
                            <p>Selamat! Pengajuan surat kuasa Anda telah disetujui. PDF surat kuasa telah tersedia untuk diunduh.</p>
                            @if($suratKuasa->pdf_file)
                                <hr>
                                <div class="text-center">
                                    <a href="{{ $suratKuasa->pdf_url }}" target="_blank" class="btn btn-success">
                                        <i class="fas fa-download me-2"></i>
                                        Download PDF Surat Kuasa
                                    </a>
                                </div>
                                <p class="mb-0 mt-2 text-center">
                                    <small>Link download juga telah dikirimkan melalui WhatsApp</small>
                                </p>
                            @endif
                        </div>
                    @elseif($suratKuasa->status === 'ditolak')
                        <div class="alert alert-danger mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-times-circle me-2"></i>
                                Pengajuan Ditolak
                            </h6>
                            @if($suratKuasa->alasan_penolakan)
                                <p><strong>Alasan Penolakan:</strong></p>
                                <p class="bg-white p-3 rounded">{{ $suratKuasa->alasan_penolakan }}</p>
                            @endif
                            <p class="mb-0">Anda dapat memperbaiki data dan mengajukan kembali surat kuasa dengan mengklik tombol di bawah.</p>
                            <hr>
                            <div class="text-center">
                                <a href="{{ route('surat-kuasa.create') }}" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i>
                                    Ajukan Ulang
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ route('surat-kuasa.track-form') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i>
                            Lacak Pengajuan Lain
                        </a>
                        <a href="{{ route('surat-kuasa.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            Buat Pengajuan Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-horizontal {
    display: flex;
    justify-content: space-between;
    margin: 30px 0;
    position: relative;
}

.timeline-horizontal::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 50px;
    right: 50px;
    height: 2px;
    background-color: #dee2e6;
    z-index: 1;
}

.timeline-step {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 2;
}

.timeline-step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #dee2e6;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    color: #6c757d;
    font-size: 20px;
    transition: all 0.3s ease;
}

.timeline-step.active .timeline-step-icon {
    background-color: #ffc107;
    color: white;
    animation: pulse 2s infinite;
}

.timeline-step.completed .timeline-step-icon {
    background-color: #28a745;
    color: white;
}

.timeline-step.rejected .timeline-step-icon {
    background-color: #dc3545;
    color: white;
}

.timeline-step-content h6 {
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.timeline-step-content small {
    color: #6c757d;
    font-size: 0.8rem;
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

@media (max-width: 768px) {
    .timeline-horizontal {
        flex-direction: column;
        align-items: center;
    }

    .timeline-horizontal::before {
        display: none;
    }

    .timeline-step {
        margin-bottom: 20px;
        flex: none;
    }

    .timeline-step::after {
        content: '';
        position: absolute;
        top: 60px;
        left: 50%;
        width: 2px;
        height: 30px;
        background-color: #dee2e6;
        transform: translateX(-50%);
    }

    .timeline-step:last-child::after {
        display: none;
    }

    .timeline-step.completed::after {
        background-color: #28a745;
    }
}
</style>
@endpush
