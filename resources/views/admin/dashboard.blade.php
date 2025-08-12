<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Admin - Surat Kuasa Online')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-none d-md-block">
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>
                        Menu Admin
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center py-3">
                <div>
                    <h2 class="mb-0">Dashboard Admin</h2>
                    <small class="text-muted">Kelola pengajuan surat kuasa</small>
                </div>
                <div>
                    <span class="text-muted">{{ now()->format('d F Y, H:i') }}</span>
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

<div class="row mb-4 g-3">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <p class="mb-0">Total Pengajuan</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-file-contract"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-warning text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    <p class="mb-0">Menunggu Verifikasi</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['disetujui'] }}</h3>
                    <p class="mb-0">Disetujui</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card bg-danger text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <h3 class="mb-0">{{ $stats['ditolak'] }}</h3>
                    <p class="mb-0">Ditolak</p>
                </div>
                <div class="fs-1">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>


            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Filter Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama, NIK, atau ID">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Pengajuan Surat Kuasa
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive mt-2">
                        <div class="table-responsive">
                            <table id="suratTable" class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Pemberi Kuasa</th>
                                        <th>Penerima Kuasa</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suratKuasas as $surat)
                                    <tr>
                                        <td><small class="font-monospace">{{ Str::limit($surat->id, 8) }}</small></td>
                                        <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <strong>{{ $surat->nama_pemberi }}</strong><br>
                                            <small class="text-muted">NIK: {{ $surat->nik_pemberi }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $surat->nama_penerima }}</strong><br>
                                            <small class="text-muted">NIK: {{ $surat->nik_penerima }}</small>
                                        </td>
                                        <td>
                                            <span class="status-badge bg-{{ $surat->status_badge_color }}">
                                                {{ $surat->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.show', $surat->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($surat->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="showApproveModal('{{ $surat->id }}')" title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="showRejectModal('{{ $surat->id }}')" title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @endif

                                                @if($surat->pdf_file)
                                                <a href="{{ route('admin.surat-kuasa.download', $surat->id) }}" class="btn btn-sm btn-outline-primary" title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($suratKuasas->hasPages())
                    <div class="card-footer">
                        {{ $suratKuasas->appends(request()->query())->links() }}
                    </div>
                    @endif
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

    @push('scripts')
    <!-- Tambahkan JS DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#suratTable').DataTable({
                responsive: true
                , pageLength: 10
                , order: [
                    [0, 'asc']
                ]
                , language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });
        });

    </script>
    <script>
        function showApproveModal(id) {
            $('#approveForm').attr('action', `/admin/surat-kuasa/${id}/approve`);
            $('#approveModal').modal('show');
        }

        function showRejectModal(id) {
            $('#rejectForm').attr('action', `/admin/surat-kuasa/${id}/reject`);
            $('#rejectModal').modal('show');
        }

        // Auto refresh every 30 seconds for pending status
        setInterval(function() {
            if (window.location.search.includes('status=pending') || window.location.search === '') {
                window.location.reload();
            }
        }, 30000);

    </script>
    @endpush
