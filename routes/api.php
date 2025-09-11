<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportSuratKuasaController;

Route::post('/exports/surat-kuasa', [ExportSuratKuasaController::class, 'export']);
