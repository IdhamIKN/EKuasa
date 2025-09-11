<?php
// app/Console/Commands/ExportSuratKuasaCommand.php

namespace App\Console\Commands;

use App\Models\SuratKuasa;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use League\Csv\Reader;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class ExportSuratKuasaCommand extends Command
{
    protected $signature = 'surat-kuasa:export {--type=daily : Export type: daily or all}';
    protected $description = 'Export SuratKuasa data to CSV and upload to Google Spreadsheet';

    public function handle()
    {
        try {
            $exportType = $this->option('type');
            $filename = $this->generateFilename($exportType);

            // Get data based on export type
            $data = $this->getData($exportType);

            if ($data->isEmpty()) {
                $this->info('No data to export for ' . $exportType);
                Log::info("SuratKuasa Export: No data found for type '{$exportType}'");
                return 0;
            }

            // Generate CSV
            $csvPath = $this->generateCsv($data, $filename);

            $this->info("CSV exported successfully: {$csvPath}");
            $this->info("Total records: " . $data->count());

            // Upload to Google Spreadsheet
            if (config('services.google.enabled', false)) {
                $this->uploadToGoogleSheets($csvPath, $filename, $exportType);
            }

            // Log the export
            Log::info("SuratKuasa Export completed", [
                'type' => $exportType,
                'filename' => $filename,
                'records_count' => $data->count(),
                'file_path' => $csvPath
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Export failed: ' . $e->getMessage());
            Log::error('SuratKuasa Export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    private function getData($exportType)
    {
        $query = SuratKuasa::query();

        if ($exportType === 'daily') {
            $today = Carbon::today();
            $query->whereDate('created_at', $today)
                  ->orWhereDate('tanggal_pengajuan', $today);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function generateFilename($exportType)
    {
        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $prefix = $exportType === 'daily' ? 'daily' : 'all';

        return "surat_kuasa_export_{$prefix}_{$date}.csv";
    }

    private function generateCsv($data, $filename)
    {
        $csvPath = storage_path("app/exports/{$filename}");

        // Ensure exports directory exists
        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $csv = Writer::createFromPath($csvPath, 'w+');

        // Add BOM for Excel compatibility
        $csv->insertOne(["\xEF\xBB\xBF"]);

        // Headers
        $headers = [
            'ID',
            'NIK Pemberi',
            'Tanggal Pengajuan',
            'Kota Pengajuan',
            'Nama Pemberi',
            'TTL Pemberi',
            'Usia Pemberi',
            'Pekerjaan Pemberi',
            'Alamat Pemberi',
            'Nama Penerima',
            'NIK Penerima',
            'Alasan',
            'No HP Pemberi',
            'Status',
            'Status Label',
            'Alasan Penolakan',
            'Foto KTP URL',
            'PDF URL',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];

        $csv->insertOne($headers);

        // Data rows
        foreach ($data as $item) {
            $csv->insertOne([
                $item->id,
                $item->nik_pemberi,
                $item->tanggal_pengajuan ? Carbon::parse($item->tanggal_pengajuan)->format('Y-m-d') : '',
                $item->kota_pengajuan,
                $item->nama_pemberi,
                $item->ttl_pemberi,
                $item->usia_pemberi,
                $item->pekerjaan_pemberi,
                $item->alamat_pemberi,
                $item->nama_penerima,
                $item->nik_penerima,
                $item->alasan,
                $item->no_hp_pemberi,
                $item->status,
                $item->status_label,
                $item->alasan_penolakan,
                $item->foto_url,
                $item->pdf_url,
                $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '',
                $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : ''
            ]);
        }

        return $csvPath;
    }

    private function uploadToGoogleSheets($csvPath, $filename, $exportType)
    {
        try {
            $this->info('Uploading to Google Spreadsheet...');

            $client = new Client();
            $client->setApplicationName('Laravel SuratKuasa Export');
            $client->setScopes([Sheets::SPREADSHEETS]);
            $client->setAuthConfig(config('services.google.service_account_path'));

            $service = new Sheets($client);
            $spreadsheetId = config('services.google.spreadsheet_id');

            // Read CSV data
            $csvReader = Reader::createFromPath($csvPath, 'r');
            $csvReader->setHeaderOffset(1); // Skip BOM
            $records = iterator_to_array($csvReader->getRecords());

            // Prepare data for Google Sheets
            $values = [];
            $headers = $csvReader->getHeader();
            $values[] = $headers;

            foreach ($records as $record) {
                $values[] = array_values($record);
            }

            // Determine sheet name
            $sheetName = $exportType === 'daily'
                ? 'Daily_' . Carbon::now()->format('Y_m_d')
                : 'All_Data_' . Carbon::now()->format('Y_m_d_H_i');

            // Create new sheet or clear existing one
            $this->createOrClearSheet($service, $spreadsheetId, $sheetName);

            // Upload data
            $range = $sheetName . '!A1';
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);

            $params = [
                'valueInputOption' => 'RAW'
            ];

            $result = $service->spreadsheets_values->update(
                $spreadsheetId,
                $range,
                $body,
                $params
            );

            $this->info("Successfully uploaded to Google Sheets: {$sheetName}");
            $this->info("Updated cells: " . $result->getUpdatedCells());

            // Format the sheet
            $this->formatSheet($service, $spreadsheetId, $sheetName);

        } catch (\Exception $e) {
            $this->error('Google Sheets upload failed: ' . $e->getMessage());
            Log::error('Google Sheets upload failed', [
                'error' => $e->getMessage(),
                'file' => $csvPath
            ]);
            throw $e;
        }
    }

    private function createOrClearSheet($service, $spreadsheetId, $sheetName)
    {
        try {
            // Try to get existing sheets
            $response = $service->spreadsheets->get($spreadsheetId);
            $sheets = $response->getSheets();

            $sheetExists = false;
            $sheetId = null;

            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    $sheetExists = true;
                    $sheetId = $sheet->getProperties()->getSheetId();
                    break;
                }
            }

            if (!$sheetExists) {
                // Create new sheet
                $requests = [
                    new \Google\Service\Sheets\Request([
                        'addSheet' => [
                            'properties' => [
                                'title' => $sheetName
                            ]
                        ]
                    ])
                ];

                $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                    'requests' => $requests
                ]);

                $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
                $this->info("Created new sheet: {$sheetName}");
            } else {
                // Clear existing sheet
                $range = $sheetName . '!A:Z';
                $service->spreadsheets_values->clear(
                    $spreadsheetId,
                    $range,
                    new \Google\Service\Sheets\ClearValuesRequest()
                );
                $this->info("Cleared existing sheet: {$sheetName}");
            }

        } catch (\Exception $e) {
            Log::error('Error creating/clearing sheet', [
                'sheet_name' => $sheetName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function formatSheet($service, $spreadsheetId, $sheetName)
    {
        try {
            // Get sheet ID
            $response = $service->spreadsheets->get($spreadsheetId);
            $sheets = $response->getSheets();

            $sheetId = null;
            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    $sheetId = $sheet->getProperties()->getSheetId();
                    break;
                }
            }

            if (!$sheetId) {
                return;
            }

            $requests = [
                // Format header row
                new \Google\Service\Sheets\Request([
                    'repeatCell' => [
                        'range' => [
                            'sheetId' => $sheetId,
                            'startRowIndex' => 0,
                            'endRowIndex' => 1
                        ],
                        'cell' => [
                            'userEnteredFormat' => [
                                'backgroundColor' => [
                                    'red' => 0.2,
                                    'green' => 0.2,
                                    'blue' => 0.2
                                ],
                                'textFormat' => [
                                    'foregroundColor' => [
                                        'red' => 1.0,
                                        'green' => 1.0,
                                        'blue' => 1.0
                                    ],
                                    'bold' => true
                                ]
                            ]
                        ],
                        'fields' => 'userEnteredFormat(backgroundColor,textFormat)'
                    ]
                ]),
                // Freeze header row
                new \Google\Service\Sheets\Request([
                    'updateSheetProperties' => [
                        'properties' => [
                            'sheetId' => $sheetId,
                            'gridProperties' => [
                                'frozenRowCount' => 1
                            ]
                        ],
                        'fields' => 'gridProperties.frozenRowCount'
                    ]
                ])
            ];

            $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);

            $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
            $this->info("Formatted sheet: {$sheetName}");

        } catch (\Exception $e) {
            Log::warning('Sheet formatting failed', [
                'sheet_name' => $sheetName,
                'error' => $e->getMessage()
            ]);
        }
    }
}
