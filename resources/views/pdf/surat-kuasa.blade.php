<!-- resources/views/pdf/surat-kuasa-f107.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SURAT KUASA – F-1.07 (A4 Cetak)</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 16mm;
            /* dikurangi supaya tidak terlalu banyak space di atas */
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: "Courier New", Courier, monospace;
            color: #000;
            font-size: 12pt;
            line-height: 1.35;
        }

        .sheet {
            /* padding atas lebih kecil */
            padding: 6mm 6mm 8mm 6mm;
            box-sizing: border-box;
            width: calc(210mm - 32mm);
            margin: 0 auto;
        }

        .kode {
            font-size: 10pt;
            margin: 0 0 4mm 0;
            /* sebelumnya 2mm, kita tambah jaraknya */
        }

        h1.title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            margin: 10mm 0 4mm 0;
            /* tambahkan margin atas 6mm supaya ada spasi dari .kode */
        }

        /* Paragraph pembuka - gunakan inline fields agar rapi */
        p.lead {
            margin: 0 0 4mm 0;
            text-align: justify;
        }

        .field-inline {
            display: inline-block;
            vertical-align: middle;
            border-bottom: 1px dotted #000;
            padding: 0 4px;
            margin: 0 6px;
            min-height: 12px;
            line-height: 1.2;
        }

        /* Tabel label / value untuk data agar rapi */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4mm;
        }

        table.data td {
            vertical-align: top;
            padding: 2px 0;
        }

        .label {
            width: 56mm;
            /* lebar tetap untuk label */
            padding-right: 4mm;
        }

        .sep {
            width: 6mm;
        }

        .value {
            width: calc(100% - 62mm);
        }

        .field-block {
            display: block;
            border-bottom: 1px dotted #000;
            padding: 2px 4px;
            min-height: 12px;
        }

        /* Supaya teks nilai tampak uppercase & rapi */
        .value .uppercase {
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Area tanda tangan: tiga kolom (kiri, tengah QR, kanan) */
        table.ttd-table {
            width: 100%;
            margin-top: 14mm;
            border-collapse: collapse;
        }

        table.ttd-table td {
            vertical-align: middle;
            padding: 0;
        }

        .ttd-left,
        .ttd-right {
            width: 45%;
            text-align: center;
        }

        .ttd-center {
            width: 15%;
            text-align: center;
        }

        .ttd-caption {
            height: 10mm;
            font-size: 12pt;
        }

        .ttd-sign {
            height: 70mm;
            /* ruang untuk tanda tangan */
            vertical-align: bottom;
            text-align: center;
        }

        .paren {
            display: inline-block;
            border-bottom: 1px solid #000;
            padding: 0 5px 2px 5px;
            /* kiri-kanan 5px, bawah 2px */
            text-align: center;
            white-space: nowrap;
        }


        .paren span {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            /* jika nama terlalu panjang, beri titik-titik */
        }

        .long-name {
            font-size: 11pt;
            /* mengecil otomatis jika perlu */
        }


        .qr {
            width: 30mm;
            height: 30mm;
            object-fit: contain;
            display: inline-block;
        }

        .footnote {
            font-size: 10pt;
            margin-top: 6mm;
        }

        /* kecilkan jarak antar baris pada tabel agar lebih rapat */
        table.data td,
        table.ttd-table td {
            line-height: 1.2;
        }

        /* Style baru untuk QR di kanan bawah */
        .qr-container {
            position: absolute;
            right: 16mm;
            bottom: 8mm;
            text-align: center;
        }

        .verifikasi-text {
            font-size: 10pt;
            margin-top: 2mm;
        }

    </style>
</head>
<body>
    <section class="sheet">
        <div class="kode">F 1.07</div>
        <h1 class="title mt-5">SURAT KUASA DALAM PELAYANAN ADMINISTRASI KEPENDUDUKAN</h1>

        <!-- Paragraf pembuka dengan field inline supaya tidak memberi spasi berlebihan -->
        <p class="lead">
            Pada hari ini
            <span class="field-inline">{{ $hari_pembuatan }}</span>
            tanggal
            <span class="field-inline" style="min-width:16mm">{{ $tanggal }}</span>
            bulan
            <span class="field-inline" style="min-width:36mm">{{ $bulan }}</span>
            tahun
            <span class="field-inline" style="min-width:24mm">{{ $tahun }}</span>
            bertempat di
            <span class="field-inline" style="min-width:60mm">{{ $surat_kuasa->kota_pengajuan ?? '' }}</span>,
            saya :
        </p>

        <!-- Data pemohon -->
        <table class="data">
            <tr>
                <td class="label">Nama lengkap</td>
                <td class="sep">:</td>
                <td class="value"><span class="field-block uppercase">{{ $surat_kuasa->nama_pemberi }}</span></td>
            </tr>
            <tr>
                <td class="label">Tempat &amp; Tanggal lahir/Usia</td>
                <td class="sep">:</td>
                <td class="value"><span class="field-block">{{ $surat_kuasa->ttl_pemberi }} / {{ $surat_kuasa->usia_pemberi }} tahun</span></td>
            </tr>
            <tr>
                <td class="label">Pekerjaan</td>
                <td class="sep">:</td>
                <td class="value"><span class="field-block">{{ $surat_kuasa->pekerjaan_pemberi }}</span></td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td class="value"><span class="field-block">{{ $surat_kuasa->alamat_pemberi }}</span></td>
            </tr>
        </table>

        <p style="margin:0 0 4mm 0">Memberikan kuasa kepada:</p>

        <!-- Data penerima -->
        <table class="data">
            <tr>
                <td class="label">Nama lengkap</td>
                <td class="sep">:</td>
                <td class="value"><span class="field-block uppercase">{{ $surat_kuasa->nama_penerima }}</span></td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="sep">:</td>
                <td class="value"><span class="field-block">{{ $surat_kuasa->nik_penerima }}</span></td>
            </tr>
        </table>

        <p style="margin:0 0 4mm 0;text-align:justify;">
            Untuk mengisi formulir dalam pelayanan administrasi kependudukan, sesuai keterangan dan kelengkapan persyaratan yang saya berikan seperti keadaan yang sebenarnya dikarenakan kondisi saya dalam keadaan
            <span class="field-inline" style="min-width:90mm">{{ $surat_kuasa->alasan }}</span>. <span style="font-size:10pt">*)</span>
        </p>

        <!-- Area tanda tangan: QR di tengah sejajar vertikal -->
        <table class="ttd-table">
            <tr>
                <td class="ttd-left ttd-caption">Yang diberi kuasa,</td>
                <td class="ttd-center"></td>
                <td class="ttd-right ttd-caption">Yang memberi kuasa,</td>
            </tr>
            <tr>
                <td class="ttd-left ttd-sign">
                    <span class="paren">
                        <span class="{{ strlen($surat_kuasa->nama_penerima) > 30 ? 'long-name' : '' }}">
                            {{ $surat_kuasa->nama_penerima }}
                        </span>
                    </span>
                </td>
                <td class="ttd-center"></td>
                <td class="ttd-right ttd-sign">
                    <span class="paren">
                        <span class="{{ strlen($surat_kuasa->nama_pemberi) > 30 ? 'long-name' : '' }}">
                            {{ $surat_kuasa->nama_pemberi }}
                        </span>
                    </span>
                </td>
            </tr>
        </table>

        <!-- QR Code dipindah ke kanan bawah dengan teks verifikasi -->
        <div class="qr-container">
            @if(!empty($qrcode))
            <img src="{{ $qrcode }}" alt="QR Code" class="qr">
            <div class="verifikasi-text">
                Dokumen ini telah terverifikasi secara elektronik<br>
                {{ date('d-m-Y H:i:s') }}
            </div>
            @endif
        </div>

        <p class="footnote">*) coret yang tidak sesuai</p>
    </section>
</body>
</html>
