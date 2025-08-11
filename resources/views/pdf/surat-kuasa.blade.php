<!-- resources/views/pdf/surat-kuasa-f107.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SURAT KUASA – F-1.07 (A4 Cetak)</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 20mm;
    }
    html, body {
      height: 100%;
    }
    body {
      font-family: "Times New Roman", Times, serif;
      color: #000;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      margin: 0;
      background: #fff;
    }
    .sheet {
      width: 210mm;
      min-height: 297mm;
      box-sizing: border-box;
      padding: 20mm;
      margin: 0 auto;
    }
    @media screen {
      body { background: #e5e5e5; }
      .sheet {
        background: #fff;
        box-shadow: 0 0 0.8mm rgba(0,0,0,0.2);
      }
    }
    h1.title {
      font-size: 15pt;
      text-align: center;
      text-transform: uppercase;
      margin: 2mm 0 6mm 0;
      letter-spacing: 0.2px;
    }
    .kode {
      font-weight: 600;
      font-size: 11pt;
      margin-bottom: 6mm;
    }
    p { margin: 0 0 3.2mm 0; line-height: 1.4; font-size: 12pt; }
    .indent { text-indent: 10mm; }
    .field {
      display: inline-block;
      border-bottom: 0.6pt dotted #000;
      min-width: 70mm;
      line-height: 1.2;
      transform: translateY(-0.6mm);
    }
    .field.short { min-width: 30mm; }
    .field.med { min-width: 50mm; }
    .field.long { min-width: 120mm; }
    .row { display: flex; gap: 2mm; }
    .label { min-width: 60mm; }
    .blok {
      margin-top: 3.2mm;
      margin-bottom: 3.2mm;
    }
    .ttd-area {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10mm;
      margin-top: 20mm;
    }
    .ttd {
      text-align: center;
      min-height: 55mm;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }
    .ttd .name-line {
      margin-top: 25mm;
    }
    .paren-line {
      display: inline-block;
      padding: 0 8mm;
      border-bottom: 0.6pt solid #000;
      min-width: 60mm;
    }
    .footnote {
      font-size: 10pt;
      margin-top: 10mm;
    }
    .avoid-break { page-break-inside: avoid; }

    /* Style untuk QR Code dan Barcode */
    .qr-section {
      position: absolute;
      top: 10mm;
      right: 20mm;
      width: 30mm;
      text-align: center;
    }
    .qr-code img {
      max-width: 25mm;
      height: auto;
    }
    .barcode {
      margin-top: 5mm;
      font-size: 8pt;
    }
    .barcode img {
      max-width: 30mm;
      height: 10mm;
    }
  </style>
</head>
<body>
  <section class="sheet" role="document" aria-label="Surat Kuasa F-1.07">

    <!-- QR Code dan Barcode di pojok kanan atas -->
    @if($qrcode || $barcode)
    <div class="qr-section">
      @if($qrcode)
        <div class="qr-code">
          <img src="{{ $qrcode }}" alt="QR Code">
        </div>
      @endif

      @if($barcode)
        <div class="barcode">
          <img src="{{ $barcode }}" alt="Barcode">
          <div style="font-size: 6pt;">{{ $nomor_surat }}</div>
        </div>
      @endif
    </div>
    @endif

    <div class="kode">F 1 .07</div>
    <h1 class="title">Surat Kuasa Dalam Pelayanan Administrasi Kependudukan</h1>

    <p>Pada hari ini <span class="field short">{{ $hari_pembuatan }}</span>
      tanggal <span class="field short">{{ $tanggal }}</span>
      bulan <span class="field short">{{ $bulan }}</span>
      tahun <span class="field short">{{ $tahun }}</span>
      bertempat di <span class="field long">{{ $surat_kuasa->kota_pengajuan ?? 'Tidak disebutkan' }}</span>, saya :</p>

    <div class="blok avoid-break">
      <p class="row"><span class="label">Nama lengkap</span>: <span class="field long">{{ strtoupper($surat_kuasa->nama_pemberi) }}</span></p>
      <p class="row"><span class="label">Tempat &amp; Tanggal lahir/Usia</span>: <span class="field long">{{ $surat_kuasa->ttl_pemberi }} / {{ $surat_kuasa->usia_pemberi }} tahun</span></p>
      <p class="row"><span class="label">Pekerjaan</span>: <span class="field long">{{ $surat_kuasa->pekerjaan_pemberi }}</span></p>
      <p class="row"><span class="label">Alamat</span>: <span class="field long">{{ $surat_kuasa->alamat_pemberi }}</span></p>
    </div>

    <p class="blok">Memberikan kuasa kepada:</p>

    <div class="blok avoid-break">
      <p class="row"><span class="label">Nama lengkap</span>: <span class="field long">{{ strtoupper($surat_kuasa->nama_penerima) }}</span></p>
      <p class="row"><span class="label">NIK</span>: <span class="field long">{{ $surat_kuasa->nik_penerima }}</span></p>
    </div>

    <p class="indent">
      Untuk mengisi formulir dalam pelayanan administrasi kependudukan, sesuai keterangan dan kelengkapan persyaratan yang saya berikan seperti keadaan yang sebenarnya dikarenakan kondisi saya dalam keadaan
      <span class="field med">{{ $surat_kuasa->alasan }}</span>. <span style="font-size:10pt">*)</span>
    </p>

    <div class="ttd-area avoid-break">
      <div class="ttd">
        <div>Yang diberi kuasa,</div>
        <div class="name-line">(<span class="paren-line">{{ strtoupper($surat_kuasa->nama_penerima) }}</span>)</div>
      </div>
      <div class="ttd">
        <div>Yang memberi kuasa,</div>
        <div class="name-line">(<span class="paren-line">{{ strtoupper($surat_kuasa->nama_pemberi) }}</span>)</div>
      </div>
    </div>

    <p class="footnote">*) coret yang tidak sesuai</p>
  </section>
</body>
</html>
