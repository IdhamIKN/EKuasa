<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Surat Kuasa Online')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        .card-form {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 15px;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            position: relative;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-top: 50px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('surat-kuasa.create') }}">
                <i class="fas fa-file-contract me-2"></i>
                Surat Kuasa Online
            </a>

            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('surat-kuasa.track-form') }}">
                    <i class="fas fa-search me-1"></i>
                    Lacak Pengajuan
                </a>
                @auth
                    @role('admin')
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard Admin
                        </a>
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Keluar
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @endrole
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Surat Kuasa Online</h5>
                    <p class="mb-0">Layanan pengajuan surat kuasa secara online yang mudah dan cepat.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">© {{ date('Y') }} Sistem Surat Kuasa Online</p>
                    <small>Dikembangkan dengan <i class="fas fa-heart text-danger"></i> untuk kemudahan masyarakat</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    @stack('scripts')
</body>
</html>
