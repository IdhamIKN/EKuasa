<!-- resources/views/surat-kuasa/create.blade.php -->
@extends('layouts.app')
@section('title', 'Pengajuan Surat Kuasa Online')
@section('content')
<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-4 mb-3">
                    <i class="fas fa-file-contract me-3"></i>
                    Pengajuan Surat Kuasa Online
                </h1>
                <p class="lead">Ajukan surat kuasa Anda dengan mudah, cepat, dan aman. Proses verifikasi akan dilakukan dalam 1x24 jam.</p>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-form border-0">
                <div class="card-body p-5">
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
                            <strong>Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <!-- Step Indicator -->
                    <div class="step-indicator mb-5">
                        <div class="step active" data-step="1">1</div>
                        <div class="step" data-step="2">2</div>
                        <div class="step" data-step="3">3</div>
                    </div>
                    <form action="{{ route('surat-kuasa.store') }}" method="POST" enctype="multipart/form-data" id="suratKuasaForm">
                        @csrf
                        <!-- Section 1: Data Pengajuan -->
                        <div class="form-section" id="section-1">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-info-circle me-2"></i>
                                Data Pengajuan
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                                    <input type="text" class="form-control" value="{{ now()->format('d F Y') }}" readonly>
                                    <small class="text-muted">Tanggal otomatis sesuai hari ini</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kota_pengajuan" class="form-label">Kota Pengajuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kota_pengajuan" name="kota_pengajuan"
                                           value="{{ old('kota_pengajuan', 'Ponorogo') }}" required>
                                </div>
                            </div>
                        </div>
                        <!-- Section 2: Data Pemberi Kuasa -->
                        <div class="form-section d-none" id="section-2">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-user me-2"></i>
                                Data Pemberi Kuasa
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nik_pemberi" class="form-label">NIK Pemberi Kuasa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik_pemberi" name="nik_pemberi"
                                           value="{{ old('nik_pemberi') }}" maxlength="16" required>
                                    <small class="text-muted">16 digit NIK sesuai KTP</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nama_pemberi" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_pemberi" name="nama_pemberi"
                                           value="{{ old('nama_pemberi') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="ttl_pemberi" class="form-label">Tempat, Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ttl_pemberi" name="ttl_pemberi"
                                           value="{{ old('ttl_pemberi') }}" placeholder="Contoh: Jakarta, 15 Agustus 1990" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="usia_pemberi" class="form-label">Usia <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="usia_pemberi" name="usia_pemberi"
                                           value="{{ old('usia_pemberi') }}" min="17" max="100" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pekerjaan_pemberi" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="pekerjaan_pemberi" name="pekerjaan_pemberi"
                                           value="{{ old('pekerjaan_pemberi') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp_pemberi" class="form-label">Nomor HP/WA <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_hp_pemberi" name="no_hp_pemberi"
                                           value="{{ old('no_hp_pemberi') }}" placeholder="Contoh: 081234567890" required>
                                    <small class="text-muted">Notifikasi akan dikirim ke nomor ini</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat_pemberi" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat_pemberi" name="alamat_pemberi" rows="3" required>{{ old('alamat_pemberi') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="foto_pemberi_ktp" class="form-label">Foto Memegang KTP Asli <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="foto_pemberi_ktp" name="foto_pemberi_ktp"
                                       accept="image/jpeg,image/png,image/jpg" required>
                                <small class="text-muted">Format: JPG, PNG. Maksimal 2MB. Pastikan foto jelas dan KTP terbaca.</small>
                                <div id="foto-preview" class="mt-2"></div>
                            </div>
                        </div>
                        <!-- Section 3: Data Penerima Kuasa & Alasan -->
                        <div class="form-section d-none" id="section-3">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-user-tie me-2"></i>
                                Data Penerima Kuasa & Alasan
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_penerima" class="form-label">Nama Penerima Kuasa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_penerima" name="nama_penerima"
                                           value="{{ old('nama_penerima') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nik_penerima" class="form-label">NIK Penerima Kuasa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik_penerima" name="nik_penerima"
                                           value="{{ old('nik_penerima') }}" maxlength="16" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="alasan" class="form-label">Kondisi/Alasan Pemberian Kuasa <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alasan" name="alasan" rows="4"
                                          placeholder="Contoh: Sedang sakit dan tidak dapat hadir secara langsung untuk mengurus..." required>{{ old('alasan') }}</textarea>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Informasi:</strong> Setelah pengajuan berhasil, Anda akan menerima notifikasi WhatsApp.
                                Proses verifikasi akan dilakukan maksimal 1x24 jam. Jika disetujui, link download PDF akan dikirimkan.
                            </div>
                        </div>
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
                                <i class="fas fa-arrow-left me-2"></i>
                                Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary" id="nextBtn">
                                Selanjutnya
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                <i class="fas fa-paper-plane me-2"></i>
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSection = 1;
const totalSections = 3;

// Multi-step form navigation
$('#nextBtn').click(function() {
    if (validateCurrentSection()) {
        if (currentSection < totalSections) {
            // Sembunyikan section saat ini
            $(`#section-${currentSection}`).addClass('d-none');

            // Pindah ke section berikutnya
            currentSection++;

            // Tampilkan section baru
            $(`#section-${currentSection}`).removeClass('d-none');

            // Update UI
            updateStepIndicator();
            updateNavigationButtons();
        }
    }
});

$('#prevBtn').click(function() {
    if (currentSection > 1) {
        // Sembunyikan section saat ini
        $(`#section-${currentSection}`).addClass('d-none');

        // Pindah ke section sebelumnya
        currentSection--;

        // Tampilkan section sebelumnya
        $(`#section-${currentSection}`).removeClass('d-none');

        // Update UI
        updateStepIndicator();
        updateNavigationButtons();
    }
});

function updateStepIndicator() {
    $('.step').removeClass('active completed');
    for (let i = 1; i <= totalSections; i++) {
        const stepElement = $(`.step[data-step="${i}"]`);
        if (i < currentSection) {
            stepElement.addClass('completed');
        } else if (i === currentSection) {
            stepElement.addClass('active');
        }
    }
}

function updateNavigationButtons() {
    // Tombol Previous: tampilkan hanya jika tidak di section pertama
    if (currentSection > 1) {
        $('#prevBtn').show();
    } else {
        $('#prevBtn').hide();
    }

    // Tombol Next: tampilkan hanya jika tidak di section terakhir
    if (currentSection < totalSections) {
        $('#nextBtn').show();
        $('#submitBtn').hide();
    } else {
        // Di section terakhir: sembunyikan Next, tampilkan Submit
        $('#nextBtn').hide();
        $('#submitBtn').show();
    }

    console.log('Current section:', currentSection, 'Total sections:', totalSections);
    console.log('Next button visible:', $('#nextBtn').is(':visible'));
    console.log('Submit button visible:', $('#submitBtn').is(':visible'));
}

function validateCurrentSection() {
    let valid = true;
    const currentSectionEl = $(`#section-${currentSection}`);

    // Reset previous validation states
    currentSectionEl.find('.is-invalid').removeClass('is-invalid');

    // Validate required fields
    currentSectionEl.find('input[required], textarea[required]').each(function() {
        const $this = $(this);
        const value = $this.val().trim();

        if (!value) {
            $this.addClass('is-invalid');
            valid = false;
            console.log('Invalid field:', $this.attr('id'), 'value:', value);
        }
    });

    // Additional validations per section
    if (currentSection === 2) {
        // NIK validation
        const nik = $('#nik_pemberi').val().trim();
        if (nik.length !== 16 || !/^\d+$/.test(nik)) {
            $('#nik_pemberi').addClass('is-invalid');
            valid = false;
            console.log('Invalid NIK pemberi:', nik);
        }

        // Age validation
        const usia = parseInt($('#usia_pemberi').val());
        if (isNaN(usia) || usia < 17 || usia > 100) {
            $('#usia_pemberi').addClass('is-invalid');
            valid = false;
            console.log('Invalid age:', usia);
        }

        // File validation
        const fileInput = $('#foto_pemberi_ktp')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            $('#foto_pemberi_ktp').addClass('is-invalid');
            valid = false;
            console.log('No file selected');
        }
    }

    if (currentSection === 3) {
        // NIK penerima validation
        const nikPenerima = $('#nik_penerima').val().trim();
        if (nikPenerima.length !== 16 || !/^\d+$/.test(nikPenerima)) {
            $('#nik_penerima').addClass('is-invalid');
            valid = false;
            console.log('Invalid NIK penerima:', nikPenerima);
        }
    }

    console.log('Section', currentSection, 'validation result:', valid);

    if (!valid) {
        // Scroll to first invalid field
        const firstInvalid = currentSectionEl.find('.is-invalid').first();
        if (firstInvalid.length) {
            $('html, body').animate({
                scrollTop: firstInvalid.offset().top - 100
            }, 300);
        }
    }

    return valid;
}

// File preview
$('#foto_pemberi_ktp').change(function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            $(this).val('');
            $('#foto-preview').empty();
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            $('#foto-preview').html(`
                <div class="mt-2">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    <p class="text-muted small mt-1">Preview foto yang akan diupload</p>
                </div>
            `);
        };
        reader.readAsDataURL(file);

        // Remove invalid class when file is selected
        $(this).removeClass('is-invalid');
    }
});

// NIK input formatting
$('#nik_pemberi, #nik_penerima').on('input', function() {
    this.value = this.value.replace(/\D/g, '');
    if (this.value.length > 16) {
        this.value = this.value.substring(0, 16);
    }

    // Remove invalid class when user types
    $(this).removeClass('is-invalid');
});

// Phone number formatting
$('#no_hp_pemberi').on('input', function() {
    this.value = this.value.replace(/\D/g, '');
    $(this).removeClass('is-invalid');
});

// Remove invalid class when user types in other fields
$('input, textarea').on('input', function() {
    $(this).removeClass('is-invalid');
});

// Form submission
$('#suratKuasaForm').submit(function(e) {
    e.preventDefault();

    if (!validateCurrentSection()) {
        return false;
    }

    // Show loading
    $('#submitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...');
    $('#submitBtn').prop('disabled', true);

    // Submit form
    this.submit();
});

// Initialize on page load
$(document).ready(function() {
    updateNavigationButtons();
    updateStepIndicator();

    console.log('Form initialized');
    console.log('Current section:', currentSection);
    console.log('Total sections:', totalSections);
});
</script>
@endpush
