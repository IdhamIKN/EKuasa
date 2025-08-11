<!-- resources/views/surat-kuasa/track-form.blade.php -->
@extends('layouts.app')

@section('title', 'Lacak Pengajuan - Surat Kuasa Online')

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-4 mb-3">
                    <i class="fas fa-search me-3"></i>
                    Lacak Status Pengajuan
                </h1>
                <p class="lead">Masukkan ID pengajuan dan NIK untuk melacak status surat kuasa Anda</p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-form border-0">
                <div class="card-body p-5">
                    <!-- Alert Messages -->
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

                    <form method="POST" action="{{ route('surat-kuasa.track') }}">
                        @csrf

                        <div class="mb-4 text-center">
                            <i class="fas fa-file-search fa-3x text-primary mb-3"></i>
                            <h4>Lacak Pengajuan Anda</h4>
                            <p class="text-muted">Dapatkan informasi terkini tentang status pengajuan surat kuasa</p>
                        </div>

                        <div class="mb-4">
                            <label for="id" class="form-label">
                                <i class="fas fa-fingerprint me-2"></i>
                                ID Pengajuan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="id" name="id"
                                   value="{{ old('id') }}" placeholder="Masukkan ID pengajuan" required>
                            <small class="text-muted">ID pengajuan yang Anda terima saat mendaftar</small>
                        </div>

                        <div class="mb-4">
                            <label for="nik" class="form-label">
                                <i class="fas fa-id-card me-2"></i>
                                NIK Pemberi Kuasa <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="nik" name="nik"
                                   value="{{ old('nik') }}" placeholder="Masukkan 16 digit NIK" maxlength="16" required>
                            <small class="text-muted">16 digit NIK sesuai yang didaftarkan</small>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i>
                                Lacak Status Pengajuan
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-2">Belum punya pengajuan?</p>
                            <a href="{{ route('surat-kuasa.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>
                                Buat Pengajuan Baru
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="card border-0 mt-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Informasi Status
                    </h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-3">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h6 class="small">Menunggu Verifikasi</h6>
                                <small class="text-muted">Pengajuan sedang diproses admin</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h6 class="small">Disetujui</h6>
                                <small class="text-muted">Surat kuasa telah selesai</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                <h6 class="small">Ditolak</h6>
                                <small class="text-muted">Perlu perbaikan data</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// NIK input formatting
$('#nik').on('input', function() {
    this.value = this.value.replace(/\D/g, '');
    if (this.value.length > 16) {
        this.value = this.value.substring(0, 16);
    }
});

// Form validation
$('form').on('submit', function(e) {
    const id = $('#id').val().trim();
    const nik = $('#nik').val().trim();

    if (!id) {
        e.preventDefault();
        alert('ID pengajuan wajib diisi');
        $('#id').focus();
        return false;
    }

    if (!nik || nik.length !== 16) {
        e.preventDefault();
        alert('NIK harus 16 digit');
        $('#nik').focus();
        return false;
    }
});
</script>
@endpush
