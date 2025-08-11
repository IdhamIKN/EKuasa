<!-- resources/views/pdf/surat-kuasa.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Kuasa</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.7;
            margin: 0;
            padding: 25px;
            color: #1a1a1a;
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .document-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="0.5" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="0.3" fill="white" opacity="0.08"/><circle cx="50" cy="10" r="0.4" fill="white" opacity="0.09"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            opacity: 0.6;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 15px 0;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .nomor-surat {
            font-size: 14px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 24px;
            border-radius: 25px;
            display: inline-block;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .main-content {
            padding: 40px;
        }

        .tanggal {
            text-align: right;
            margin-bottom: 30px;
            font-weight: 500;
            color: #4a5568;
            font-size: 12px;
        }

        .document-title {
            text-align: center;
            font-weight: 700;
            font-size: 18px;
            margin: 30px 0 40px 0;
            color: #2d3748;
            position: relative;
            padding-bottom: 15px;
        }

        .document-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .content p {
            text-align: justify;
            margin-bottom: 20px;
            color: #2d3748;
            font-weight: 400;
        }

        .data-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .section-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .data-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 12px;
            padding: 8px 0;
        }

        .data-row:not(:last-child) {
            border-bottom: 1px solid #e2e8f0;
        }

        .data-row .label {
            flex: 0 0 180px;
            font-weight: 500;
            color: #4a5568;
            font-size: 11px;
        }

        .data-row .separator {
            flex: 0 0 15px;
            color: #718096;
        }

        .data-row .value {
            flex: 1;
            font-weight: 600;
            color: #1a202c;
        }

        .purpose-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
        }

        .purpose-section strong {
            font-size: 14px;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 15px;
        }

        .purpose-content {
            font-size: 12px;
            line-height: 1.8;
            margin-left: 0;
        }

        .signature-section {
            margin-top: 50px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 30px;
        }

        .signature-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 30px 0;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            position: relative;
        }

        .signature-role {
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .signature-space {
            height: 100px;
            border: 2px dashed #cbd5e0;
            margin: 20px 10px;
            border-radius: 8px;
            background: white;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-space::before {
            content: 'Tanda Tangan';
            color: #a0aec0;
            font-size: 10px;
            font-style: italic;
        }

        .signature-name {
            font-weight: 700;
            color: #2d3748;
            margin-top: 15px;
            font-size: 12px;
        }

        .qr-section {
            margin-top: 50px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .qr-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.03) 10px,
                rgba(255, 255, 255, 0.03) 20px
            );
            animation: moveStripes 20s linear infinite;
        }

        @keyframes moveStripes {
            0% { transform: translateX(-50px) translateY(-50px); }
            100% { transform: translateX(0) translateY(0); }
        }

        .qr-content {
            position: relative;
            z-index: 2;
        }

        .qr-title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .qr-code-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            display: inline-block;
            margin: 20px 0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .qr-code-container img {
            display: block;
            height: 80px;
            width: 80px;
        }

        .verification-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 25px;
            text-align: left;
        }

        .info-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .info-label {
            font-size: 10px;
            opacity: 0.8;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 600;
            font-size: 11px;
        }

        .footer-info {
            margin-top: 40px;
            font-size: 10px;
            text-align: center;
            color: #718096;
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .footer-info p {
            margin: 8px 0;
            line-height: 1.6;
        }

        .note-section {
            background: #fff5f5;
            border-left: 4px solid #f56565;
            border-radius: 0 8px 8px 0;
            padding: 20px;
            margin: 30px 0;
        }

        .note-section strong {
            color: #c53030;
            font-size: 12px;
        }

        .note-section p {
            margin: 10px 0 0 0;
            font-style: italic;
            color: #744210;
            font-size: 11px;
        }

        /* Print styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .document-container {
                box-shadow: none;
                border-radius: 0;
            }

            .qr-section::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1>Surat Kuasa</h1>
                <div class="nomor-surat">
                    Nomor: {{ $nomor_surat }}
                </div>
            </div>
        </div>

        <div class="main-content">
            <!-- Tanggal -->
            <div class="tanggal">
                📍 {{ $surat_kuasa->kota_pengajuan }}, {{ $tanggal_pembuatan }}
            </div>

            <!-- Document Title -->
            <div class="document-title">
                SURAT KUASA KHUSUS
            </div>

            <!-- Content -->
            <div class="content">
                <p><strong>Yang bertanda tangan di bawah ini:</strong></p>

                <!-- Data Pemberi Kuasa -->
                <div class="data-section">
                    <div class="section-title">👤 Data Pemberi Kuasa</div>
                    <div class="data-row">
                        <span class="label">Nama Lengkap</span>
                        <span class="separator">:</span>
                        <span class="value">{{ strtoupper($surat_kuasa->nama_pemberi) }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Tempat, Tanggal Lahir</span>
                        <span class="separator">:</span>
                        <span class="value">{{ $surat_kuasa->ttl_pemberi }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Usia</span>
                        <span class="separator">:</span>
                        <span class="value">{{ $surat_kuasa->usia_pemberi }} tahun</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Pekerjaan</span>
                        <span class="separator">:</span>
                        <span class="value">{{ $surat_kuasa->pekerjaan_pemberi }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">NIK</span>
                        <span class="separator">:</span>
                        <span class="value">{{ $surat_kuasa->nik_pemberi }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">Alamat Lengkap</span>
                        <span class="separator">:</span>
                        <span class="value">{{ $surat_kuasa->alamat_pemberi }}</span>
                    </div>
                </div>

                <p><strong>Dengan ini memberikan kuasa kepada:</strong></p>

                <!-- Data Penerima Kuasa -->
                <div class="data-section">
                    <div class="section-title">👥 Data Penerima Kuasa</div>
                    <div class="data-row">
                        <span class="label">Nama Lengkap</span>
                        <span class="separator">:</span>
                        <span class="value">{{ strtoupper($surat_kuasa->nama_penerima) }}</span>
                    </div>
                    <div class="data-row">
                        <span class="label">NIK</span>
                        <span class="separator">:</span>
                        <span class="value">{{ $surat_kuasa->nik_penerima }}</span>
                    </div>
                </div>

                <!-- Purpose Section -->
                <div class="purpose-section">
                    <strong>🎯 UNTUK KEPERLUAN:</strong>
                    <div class="purpose-content">
                        {{ $surat_kuasa->alasan }}
                    </div>
                </div>

                <p>Demikian surat kuasa ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya dan dengan penuh tanggung jawab.</p>

                <!-- Note Section -->
                <div class="note-section">
                    <strong>📝 Catatan Penting:</strong>
                    <p>Surat kuasa ini dibuat atas dasar {{ $surat_kuasa->alasan }} dan berlaku khusus untuk keperluan tersebut di atas. Kuasa ini tidak dapat dialihkan kepada pihak lain.</p>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div class="signature-role">Yang Menerima Kuasa</div>
                            <div class="signature-space"></div>
                            <div class="signature-name">{{ strtoupper($surat_kuasa->nama_penerima) }}</div>
                        </td>
                        <td>
                            <div class="signature-role">Yang Memberi Kuasa</div>
                            <div class="signature-space"></div>
                            <div class="signature-name">{{ strtoupper($surat_kuasa->nama_pemberi) }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="qr-content">
                    <div class="qr-title">🔐 DOKUMEN RESMI - DILENGKAPI VERIFIKASI DIGITAL</div>
                    <div class="qr-code-container">
                        <img src="{{ $qrcode }}" style="width: 150px; height: 150px;" alt="QR Code Verifikasi" />
                    </div>

                    <div class="verification-info">
                        <div class="info-item">
                            <div class="info-label">ID Verifikasi</div>
                            <div class="info-value">#{{ str_pad($surat_kuasa->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Penerbitan</div>
                            <div class="info-value">{{ $tanggal_pembuatan }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="footer-info">
                <p><strong>🛡️ Keamanan Dokumen:</strong> Surat kuasa ini dibuat melalui sistem digital yang terverifikasi dan dilengkapi dengan QR Code untuk validasi keaslian.</p>
                <p><strong>📞 Verifikasi:</strong> Untuk memverifikasi keaslian dokumen ini, silakan scan QR Code di atas atau hubungi instansi terkait dengan menyertakan ID Verifikasi.</p>
                <p style="margin-top: 15px; font-style: italic; opacity: 0.8;">Dokumen ini dicetak secara otomatis dari sistem dan sah tanpa tanda tangan basah pejabat yang berwenang.</p>
            </div>
        </div>
    </div>
</body>
</html>
