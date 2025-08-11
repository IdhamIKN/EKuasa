<?php
// routes/web.php - Versi dengan middleware custom
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratKuasaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes - Surat Kuasa
Route::get('/', [SuratKuasaController::class, 'create'])->name('surat-kuasa.create');
Route::post('/surat-kuasa', [SuratKuasaController::class, 'store'])->name('surat-kuasa.store');
Route::get('/surat-kuasa/success/{id}', [SuratKuasaController::class, 'success'])->name('surat-kuasa.success');

// Track Routes
Route::get('/lacak', [SuratKuasaController::class, 'showTrackForm'])->name('surat-kuasa.track-form');
Route::post('/lacak', [SuratKuasaController::class, 'track'])->name('surat-kuasa.track');

// Manual Auth Routes - Hanya Login/Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route untuk verifikasi surat kuasa via QR Code
Route::get('/verify-surat/{id}', [SuratKuasaController::class, 'verifySurat'])
    ->name('surat-kuasa.verify');

// Admin Routes - Menggunakan middleware custom
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/surat-kuasa/{id}', [AdminController::class, 'show'])->name('admin.show');
    Route::post('/surat-kuasa/{id}/approve', [AdminController::class, 'approve'])->name('admin.approve');
    Route::post('/surat-kuasa/{id}/reject', [AdminController::class, 'reject'])->name('admin.reject');
    //
    Route::get('/surat-kuasa/{id}/preview-pdf', [AdminController::class, 'previewPDF'])
        ->name('admin.surat-kuasa.preview');

    Route::get('/surat-kuasa/{id}/generate-pdf', [AdminController::class, 'generatePDF'])
        ->name('admin.surat-kuasa.generate');

    Route::get('/surat-kuasa/{id}/download-pdf', [AdminController::class, 'downloadPDF'])
        ->name('admin.surat-kuasa.download');
});

// Admin access redirect
Route::get('/admin', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
})->name('admin.access');

// Redirect home to main form
Route::get('/home', function () {
    return redirect()->route('surat-kuasa.create');
});
