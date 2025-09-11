<?php

namespace App\Services;

use App\Models\SuratKuasa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SuratKuasaExportService
{
    /**
     * Export data Surat Kuasa ke CSV.
     *
     * @param  'today'|'all'  $scope
     * @param  bool $uploadToSheets
     * @return array{path:string, filename:string, rows:int, scope:string, sheets?:array}
     */
    public function export(string $scope = 'today', bool $uploadToSheets = false): array
    {
        // Tentukan query
        $query = SuratKuasa::query();

        if ($scope === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        // Tentukan kolom yang ingin diekspor (urut & eksplisit)
        $columns = [
            'id',
            'nik_pemberi',
            'tanggal_pengajuan',
            'kota_pengajuan',
            'nama_pemberi',
            'ttl_pemberi',
            'usia_pemberi',
            'pekerjaan_pemberi',
            'alamat_pemberi',
            'nama_penerima',
            'nik_penerima',
            'alasan',
            'foto_pemberi_ktp',
            'no_hp_pemberi',
            'status',
            'alasan_penolakan',
            'pdf_file',
            'created_at',
            'updated_at',
        ];

        $rows = $query->get($columns);

        // Siapkan CSV
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $suffix = $scope === 'today' ? 'today' : 'all';
        $filename = "surat_kuasa_{$suffix}_{$timestamp}.csv";
        $path = "exports/{$filename}";

        // Tulis CSV aman dengan enclosure
        $handle = fopen('php://temp', 'r+');
        // header
        fputcsv($handle, $columns);
        // data
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $col) {
                $value = $row->{$col};
                if ($value instanceof \DateTimeInterface) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $line[] = $value;
            }
            fputcsv($handle, $line);
        }
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        Storage::disk('local')->put($path, $csvContent);

        $result = [
            'path'     => storage_path("app/{$path}"),
            'filename' => $filename,
            'rows'     => $rows->count(),
            'scope'    => $scope,
        ];

        // Opsional: Upload ke Google Sheets
        if ($uploadToSheets) {
            $result['sheets'] = $this->uploadToGoogleSheets($columns, $rows);
        }

        return $result;
    }

    /**
     * Upload data ke Google Sheets.
     * Membutuhkan:
     * - composer require google/apiclient:^2.17
     * - file kredensial di storage/app/google/credentials.json
     * - ENV: SHEETS_SPREADSHEET_ID, SHEETS_SHEET_NAME
     */
    protected function uploadToGoogleSheets(array $columns, $rows): array
    {
        $spreadsheetId = config('services.google.sheets_spreadsheet_id') ?? env('SHEETS_SPREADSHEET_ID');
        $sheetName     = config('services.google.sheets_sheet_name') ?? env('SHEETS_SHEET_NAME', 'Sheet1');
        $credPath      = storage_path('app/google/credentials.json');

        if (!file_exists($credPath)) {
            return ['ok' => false, 'message' => 'Google credentials not found at '.$credPath];
        }
        if (!$spreadsheetId) {
            return ['ok' => false, 'message' => 'SHEETS_SPREADSHEET_ID is not set'];
        }

        // Inisialisasi Google Client
        $client = new \Google\Client();
        $client->setApplicationName('Laravel SuratKuasa Export');
        $client->setScopes([\Google\Service\Sheets::SPREADSHEETS]);
        $client->setAuthConfig($credPath);
        $client->setAccessType('offline');

        $service = new \Google\Service\Sheets($client);

        // Siapkan values: header + data
        $values = [];
        $values[] = $columns;
        foreach ($rows as $row) {
            $line = [];
            foreach ($columns as $col) {
                $value = $row->{$col};
                if ($value instanceof \DateTimeInterface) {
                    $value = $value->format('Y-m-d H:i:s');
                }
                $line[] = $value;
            }
            $values[] = $line;
        }

        // Append ke sheet
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => $values
        ]);

        $range = $sheetName; // append ke sheet, mulai dari next empty row
        $params = [
            'valueInputOption' => 'RAW',
            'insertDataOption' => 'INSERT_ROWS',
        ];

        $response = $service->spreadsheets_values->append(
            $spreadsheetId,
            $range,
            $body,
            $params
        );

        return [
            'ok' => true,
            'updatedRange' => $response->getUpdates()?->getUpdatedRange(),
            'updatedRows'  => $response->getUpdates()?->getUpdatedRows(),
        ];
    }
}
