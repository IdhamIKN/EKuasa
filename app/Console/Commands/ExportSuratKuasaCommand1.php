<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SuratKuasaExportService;

class ExportSuratKuasaCommand1 extends Command
{
    protected $signature = 'export:suratkuasa {--today : Hanya data yang dibuat hari ini} {--sheets : Upload juga ke Google Sheets}';
    protected $description = 'Export data Surat Kuasa ke CSV (opsional: upload ke Google Sheets)';

    public function handle(SuratKuasaExportService $service)
    {
        $scope = $this->option('today') ? 'today' : 'all';
        $uploadToSheets = (bool) $this->option('sheets');

        $result = $service->export($scope, $uploadToSheets);

        $this->info("Export selesai: {$result['filename']}");
        $this->info("Lokasi file : {$result['path']}");
        $this->info("Jumlah baris: {$result['rows']}");

        if (!empty($result['sheets'])) {
            if ($result['sheets']['ok'] ?? false) {
                $this->info("Sheets updated: {$result['sheets']['updatedRange']} (rows: {$result['sheets']['updatedRows']})");
            } else {
                $this->warn("Sheets upload skipped/failed: " . ($result['sheets']['message'] ?? 'unknown'));
            }
        }

        return self::SUCCESS;
    }
}
