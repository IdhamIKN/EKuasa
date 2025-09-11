<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SuratKuasaExportService;

class ExportSuratKuasaController extends Controller
{
    /**
     * POST /api/exports/surat-kuasa
     * Body JSON:
     * {
     *   "scope": "today" | "all",   // default: today
     *   "upload_to_sheets": true|false  // default: false
     * }
     */
    public function export(Request $request, SuratKuasaExportService $service)
    {
        $validated = $request->validate([
            'scope' => 'nullable|in:today,all',
            'upload_to_sheets' => 'nullable|boolean',
        ]);

        $scope = $validated['scope'] ?? 'today';
        $upload = (bool) ($validated['upload_to_sheets'] ?? false);

        $result = $service->export($scope, $upload);

        return response()->json([
            'success'  => true,
            'message'  => 'Export completed',
            'data'     => [
                'filename' => $result['filename'],
                'rows'     => $result['rows'],
                'scope'    => $result['scope'],
                // Untuk keamanan, jangan expose full path; cukup relative path
                'storage_relative_path' => 'exports/' . $result['filename'],
                'sheets'   => $result['sheets'] ?? null,
                // (Opsional) URL download kalau pakai storage:link
                'download_url' => $this->publicUrlIfAvailable('exports/' . $result['filename']),
            ],
        ]);
    }

    protected function publicUrlIfAvailable(string $relativePath): ?string
    {
        // Jika kamu pakai "php artisan storage:link" dan pindah disk ke 'public'
        // tinggal ubah penyimpanan file ke disk('public').put('exports/...')
        // lalu di sini return asset('storage/'.$relativePath)
        return null;
    }
}
