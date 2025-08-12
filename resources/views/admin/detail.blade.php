<!-- resources/views/admin/detail.blade.php -->
@extends('layouts.app')

@section('title', 'Detail Pengajuan - Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>
                        Menu Admin
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div>
                    <h2 class="mb-0">Detail Pengajuan Surat Kuasa</h2>
                    <small class="text-muted">ID: {{ $suratKuasa->id }}</small>
                </div>
                <div>
                    <span class="status-badge bg-{{ $suratKuasa->status_badge_color }}">
                        {{ $suratKuasa->status_label }}
                    </span>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong>
                @foreach($errors->all() as $error)
                {{ $error }}
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="row">
                <!-- Data Pengajuan -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Pengajuan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>ID Pengajuan:</strong></td>
                                            <td><code>{{ $suratKuasa->id }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Pengajuan:</strong></td>
                                            <td>{{ $suratKuasa->tanggal_pengajuan_formatted }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kota Pengajuan:</strong></td>
                                            <td>{{ $suratKuasa->kota_pengajuan }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="status-badge bg-{{ $suratKuasa->status_badge_color }}">
                                                    {{ $suratKuasa->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Waktu Pengajuan:</strong></td>
                                            <td>{{ $suratKuasa->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Terakhir Update:</strong></td>
                                            <td>{{ $suratKuasa->updated_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. HP/WA:</strong></td>
                                            <td>{{ $suratKuasa->no_hp_pemberi }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Pemberi Kuasa -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Data Pemberi Kuasa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Nama Lengkap:</strong></td>
                                            <td>{{ $suratKuasa->nama_pemberi }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIK:</strong></td>
                                            <td>{{ $suratKuasa->nik_pemberi }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>TTL:</strong></td>
                                            <td>{{ $suratKuasa->ttl_pemberi }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Usia:</strong></td>
                                            <td>{{ $suratKuasa->usia_pemberi }} tahun</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Pekerjaan:</strong></td>
                                            <td>{{ $suratKuasa->pekerjaan_pemberi }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alamat:</strong></td>
                                            <td>{{ $suratKuasa->alamat_pemberi }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Penerima Kuasa -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie me-2"></i>
                                Data Penerima Kuasa
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="20%"><strong>Nama Lengkap:</strong></td>
                                    <td>{{ $suratKuasa->nama_penerima }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIK:</strong></td>
                                    <td>{{ $suratKuasa->nik_penerima }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Alasan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Alasan Pemberian Kuasa
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $suratKuasa->alasan }}</p>
                        </div>
                    </div>

                    @if($suratKuasa->status === 'ditolak' && $suratKuasa->alasan_penolakan)
                    <!-- Alasan Penolakan -->
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-times-circle me-2"></i>
                                Alasan Penolakan
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $suratKuasa->alasan_penolakan }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Panel -->
                <div class="col-lg-4">
                    <!-- Foto KTP -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-image me-2"></i>
                                Foto KTP
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            @if($suratKuasa->foto_pemberi_ktp)
                            <img src="{{ $suratKuasa->foto_url }}" class="img-fluid rounded" alt="Foto KTP">
                            <div class="mt-2">
                                <a href="{{ $suratKuasa->foto_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    Lihat Full Size
                                </a>
                            </div>
                            @else
                            <p class="text-muted text-center">Foto tidak tersedia</p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>
                                Aksi
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($suratKuasa->status === 'pending')
                            <button type="button" class="btn btn-success btn-block mb-2 w-100" onclick="showApproveModal('{{ $suratKuasa->id }}')">
                                <i class="fas fa-check me-2"></i>
                                Setujui Pengajuan
                            </button>
                            <button type="button" class="btn btn-danger btn-block w-100" onclick="showRejectModal('{{ $suratKuasa->id }}')">
                                <i class="fas fa-times me-2"></i>
                                Tolak Pengajuan
                            </button>
                            @elseif($suratKuasa->status === 'disetujui')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Pengajuan telah disetujui
                            </div>
                            @if($suratKuasa->pdf_file)
                            <a href="{{ route('admin.surat-kuasa.generate', $suratKuasa->id) }}" class="btn btn-primary btn-block mt-2 w-100">
                                <i class="fas fa-download me-2"></i>
                                Generate PDF
                            </a>
                            <a href="{{ route('admin.surat-kuasa.download', $suratKuasa->id) }}" class="btn btn-primary btn-block mt-2 w-100">
                                <i class="fas fa-download me-2"></i>
                                Download PDF
                            </a>
                            @endif
                            @elseif($suratKuasa->status === 'ditolak')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                Pengajuan telah ditolak
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Timeline
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Pengajuan Dibuat</h6>
                                        <small class="text-muted">{{ $suratKuasa->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @if($suratKuasa->status !== 'pending')
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $suratKuasa->status === 'disetujui' ? 'success' : 'danger' }}"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">
                                            {{ $suratKuasa->status === 'disetujui' ? 'Disetujui' : 'Ditolak' }}
                                        </h6>
                                        <small class="text-muted">{{ $suratKuasa->updated_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Konfirmasi Persetujuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyetujui pengajuan surat kuasa ini?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Setelah disetujui, sistem akan otomatis:
                    <ul class="mb-0 mt-2">
                        <li>Generate PDF surat kuasa dengan barcode</li>
                        <li>Mengirim notifikasi WhatsApp beserta link download</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" style="display: inline;" id="approveForm">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>
                        Ya, Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    Konfirmasi Penolakan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <p>Berikan alasan penolakan untuk pengajuan surat kuasa ini:</p>
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" placeholder="Contoh: Foto KTP tidak jelas, data tidak lengkap, dll." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Alasan penolakan akan dikirimkan ke pemohon melalui WhatsApp
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -22px;
        top: 20px;
        bottom: -20px;
        width: 2px;
        background-color: #dee2e6;
    }

    .timeline-item:last-child:before {
        display: none;
    }

    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #dee2e6;
    }

    .timeline-content h6 {
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

</style>
@endpush

@push('scripts')
<script>
    function showApproveModal(id) {
        $('#approveForm').attr('action', `/admin/surat-kuasa/${id}/approve`);
        $('#approveModal').modal('show');
    }

    function showRejectModal(id) {
        $('#rejectForm').attr('action', `/admin/surat-kuasa/${id}/reject`);
        $('#rejectModal').modal('show');
    }

</script>
@endpush
