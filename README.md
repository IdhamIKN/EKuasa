# Sistem Pengajuan Surat Kuasa Online

Sistem pengajuan surat kuasa secara online dengan fitur verifikasi admin, notifikasi WhatsApp otomatis, dan generate PDF dengan barcode.

## 🚀 Fitur Utama

- ✅ Form pengajuan surat kuasa tanpa login (public access)
- ✅ Dashboard admin untuk verifikasi pengajuan
- ✅ Notifikasi WhatsApp otomatis untuk setiap perubahan status
- ✅ Generate PDF surat kuasa dengan barcode verifikasi
- ✅ Tracking status pengajuan
- ✅ Upload dan validasi foto KTP
- ✅ Multi-step form dengan validasi real-time
- ✅ Responsive design dengan Bootstrap 5

## 📋 Requirements

- PHP 8.1 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Web server (Apache/Nginx)

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/IdhamIKN/EKuasa.git
cd surat-kuasa-online
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Install Additional Packages

```bash
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require milon/barcode
composer require intervention/image
```

### 4. Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

Edit file `.env` sesuai konfigurasi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=surat_kuasa_online
DB_USERNAME=root
DB_PASSWORD=
```

### 6. WhatsApp API Configuration

Tambahkan konfigurasi WhatsApp API di file `.env`:

```env
WHATSAPP_API_URL=https://.id/send-message
WHATSAPP_API_KEY=
SYSTEM_PHONE_NUMBER=6281234567890
SYSTEM_LOCATION=""
```

### 7. Publish Package Configurations

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 8. Run Migrations

```bash
php artisan migrate
```

### 9. Create Storage Link

```bash
php artisan storage:link
```

### 10. Set Permissions (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
```

### 11. Compile Assets

```bash
npm run build
```

## 👨‍💻 Default Admin Login

Setelah instalasi, gunakan kredensial berikut untuk login admin:

- **Email**: `admin@suratkuasa.com`
- **Password**: `admin123`

> **⚠️ PENTING**: Segera ganti password default setelah login pertama!

## 📁 Struktur Database

### Tabel `surat_kuasa`

| Field | Type | Description |
|-------|------|-------------|
| id | UUID | Primary key |
| nik_pemberi | VARCHAR(16) | NIK pemberi kuasa |
| tanggal_pengajuan | DATE | Tanggal pengajuan |
| kota_pengajuan | VARCHAR | Kota tempat mengajukan |
| nama_pemberi | VARCHAR | Nama lengkap pemberi kuasa |
| ttl_pemberi | VARCHAR | Tempat tanggal lahir |
| usia_pemberi | INTEGER | Usia pemberi kuasa |
| pekerjaan_pemberi | VARCHAR | Pekerjaan pemberi kuasa |
| alamat_pemberi | TEXT | Alamat lengkap |
| nama_penerima | VARCHAR | Nama penerima kuasa |
| nik_penerima | VARCHAR(16) | NIK penerima kuasa |
| alasan | TEXT | Alasan pemberian kuasa |
| foto_pemberi_ktp | VARCHAR | Path foto KTP |
| no_hp_pemberi | VARCHAR(20) | Nomor HP/WhatsApp |
| status | ENUM | pending/disetujui/ditolak |
| alasan_penolakan | TEXT | Alasan jika ditolak |
| pdf_file | VARCHAR | Path file PDF |

## 🔄 Alur Sistem

1. **Pengajuan**: Masyarakat mengisi form pengajuan surat kuasa
2. **Notifikasi**: Sistem mengirim notifikasi WhatsApp konfirmasi pengajuan
3. **Verifikasi**: Admin melakukan verifikasi data di dashboard
4. **Approval/Rejection**:
   - Jika **disetujui**: Generate PDF + kirim link download via WhatsApp
   - Jika **ditolak**: Kirim alasan penolakan via WhatsApp
5. **Tracking**: Pemohon dapat melacak status dengan ID pengajuan + NIK

## 🛡️ Keamanan

- Form validation di frontend dan backend
- File upload dengan validasi tipe dan ukuran
- Role-based access control untuk admin
- UUID untuk ID pengajuan (tidak mudah ditebak)
- Verifikasi NIK untuk tracking

## 📱 Fitur WhatsApp

### Format Pesan Notifikasi

**Pengajuan Baru:**
```
🔔 SURAT KUASA ONLINE

Halo [Nama],

Pengajuan surat kuasa Anda telah diterima dengan detail:
• ID Pengajuan: [ID]
• Tanggal: [Tanggal]
• Status: Menunggu Verifikasi

Mohon tunggu proses verifikasi dari admin.
```

**Disetujui:**
```
✅ SURAT KUASA DISETUJUI

Halo [Nama],

Selamat! Pengajuan surat kuasa Anda telah DISETUJUI.
📄 Download Surat Kuasa: [Link PDF]

Surat kuasa dilengkapi barcode sebagai tanda sah.
```

**Ditolak:**
```
❌ SURAT KUASA DITOLAK

Halo [Nama],

Pengajuan surat kuasa Anda telah DITOLAK.
Alasan: [Alasan Penolakan]

Anda dapat mengajukan kembali setelah memperbaiki data.
```

## 📊 Dashboard Admin

- **Statistik**: Total, Pending, Disetujui, Ditolak
- **Filter**: Status dan pencarian
- **Aksi**: View detail, Approve, Reject, Download PDF
- **Real-time**: Auto refresh untuk status pending

## 📄 Generate PDF

PDF surat kuasa include:
- Header resmi dengan nomor surat
- Data lengkap pemberi dan penerima kuasa
- Alasan pemberian kuasa
- Area tanda tangan
- Barcode verifikasi di footer
- ID verifikasi untuk validasi

## 🔍 Tracking System

Pemohon dapat melacak status dengan:
- ID Pengajuan (UUID)
- NIK Pemberi Kuasa (16 digit)

Timeline tracking menampilkan:
- Pengajuan dikirim
- Status verifikasi
- PDF tersedia (jika disetujui)

## ⚙️ Konfigurasi Lanjutan

### Custom WhatsApp Gateway

Jika menggunakan gateway WhatsApp lain, edit `app/Services/WhatsAppService.php`:

```php
public function sendMessage($phoneNumber, $message)
{
    // Sesuaikan dengan API gateway Anda
    $response = Http::post($this->apiUrl, [
        'api_key' => $this->apiKey,
        'receiver' => $formattedPhone,
        'data' => [
            'message' => $message
        ]
    ]);
}
```

### Custom PDF Template

Template PDF dapat dimodifikasi di `resources/views/pdf/surat-kuasa.blade.php`

### Storage Configuration

File upload disimpan di `storage/app/public/`:
- Foto KTP: `ktp-photos/`
- PDF Surat: `surat-kuasa/`

## 🚨 Troubleshooting

### Error Permission Denied
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### WhatsApp API Not Working
- Cek koneksi internet
- Verifikasi API key dan endpoint
- Cek format nomor HP (harus +62xxx)

### PDF Generation Error
```bash
# Install missing fonts (Ubuntu/Debian)
sudo apt-get install php-gd php-zip
```

### Database Connection Error
- Cek konfigurasi `.env`
- Pastikan MySQL service running
- Verifikasi user database memiliki privileges

## 📞 Support

Jika menemui masalah, silakan:
1. Cek log error di `storage/logs/laravel.log`
2. Pastikan semua requirements terpenuhi
3. Verifikasi konfigurasi `.env`

## 📝 License

MIT License. Silakan gunakan dan modifikasi sesuai kebutuhan.

---

**Dikembangkan dengan ❤️ untuk kemudahan masyarakat dalam pengurusan surat kuasa**
