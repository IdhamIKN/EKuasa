<?php
// app/Services/GoogleSheetsService.php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Drive;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleSheetsService
{
    protected $client;
    protected $sheetsService;
    protected $driveService;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->initializeClient();
        $this->spreadsheetId = config('services.google.spreadsheet_id');
    }

    private function initializeClient()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Laravel SuratKuasa Export');
        $this->client->setScopes([Sheets::SPREADSHEETS, Drive::DRIVE]);
        $this->client->setAuthConfig(config('services.google.service_account_path'));

        $this->sheetsService = new Sheets($this->client);
        $this->driveService = new Drive($this->client);
    }

    /**
     * Upload CSV data to Google Spreadsheet
     */
    public function uploadCsvToSheet($csvPath, $filename, $exportType = 'daily')
    {
        try {
            // Read CSV data
            $csvData = $this->readCsvFile($csvPath);

            // Determine sheet name
            $sheetName = $this->generateSheetName($exportType);

            // Create or clear sheet
            $this->createOrClearSheet($sheetName);

            // Upload data
            $this->uploadDataToSheet($sheetName, $csvData);

            // Format sheet
            $this->formatSheet($sheetName);

            // Add metadata
            $this->addMetadata($sheetName, $filename, count($csvData) - 1); // -1 for header

            Log::info('Successfully uploaded to Google Sheets', [
                'sheet_name' => $sheetName,
                'rows_uploaded' => count($csvData),
                'file' => $filename
            ]);

            return [
                'success' => true,
                'sheet_name' => $sheetName,
                'rows_uploaded' => count($csvData) - 1,
                'spreadsheet_url' => "https://docs.google.com/spreadsheets/d/{$this->spreadsheetId}"
            ];

        } catch (\Exception $e) {
            Log::error('Google Sheets upload failed', [
                'error' => $e->getMessage(),
                'file' => $csvPath
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function readCsvFile($csvPath)
    {
        $csvData = [];

        if (($handle = fopen($csvPath, 'r')) !== false) {
            // Skip BOM if present
            $firstLine = fgets($handle);
            if (substr($firstLine, 0, 3) !== "\xEF\xBB\xBF") {
                fseek($handle, 0);
            } else {
                $firstLine = substr($firstLine, 3);
                $csvData[] = str_getcsv($firstLine);
            }

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        return $csvData;
    }

    private function generateSheetName($exportType)
    {
        $date = Carbon::now();

        return match($exportType) {
            'daily' => 'Daily_' . $date->format('Y_m_d'),
            'all' => 'All_Data_' . $date->format('Y_m_d_H_i'),
            default => 'Export_' . $date->format('Y_m_d_H_i')
        };
    }

    private function createOrClearSheet($sheetName)
    {
        try {
            $spreadsheet = $this->sheetsService->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();

            $sheetExists = false;
            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    $sheetExists = true;
                    break;
                }
            }

            if (!$sheetExists) {
                $this->createNewSheet($sheetName);
            } else {
                $this->clearSheet($sheetName);
            }

        } catch (\Exception $e) {
            Log::error('Error creating/clearing sheet', [
                'sheet_name' => $sheetName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function createNewSheet($sheetName)
    {
        $requests = [
            new \Google\Service\Sheets\Request([
                'addSheet' => [
                    'properties' => [
                        'title' => $sheetName,
                        'gridProperties' => [
                            'rowCount' => 1000,
                            'columnCount' => 26
                        ]
                    ]
                ]
            ])
        ];

        $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
            'requests' => $requests
        ]);

        $this->sheetsService->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
    }

    private function clearSheet($sheetName)
    {
        $range = $sheetName . '!A:Z';
        $this->sheetsService->spreadsheets_values->clear(
            $this->spreadsheetId,
            $range,
            new \Google\Service\Sheets\ClearValuesRequest()
        );
    }

    private function uploadDataToSheet($sheetName, $data)
    {
        $range = $sheetName . '!A1';
        $body = new \Google\Service\Sheets\ValueRange([
            'values' => $data
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $this->sheetsService->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }

    private function formatSheet($sheetName)
    {
        try {
            $sheetId = $this->getSheetId($sheetName);
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
                                    'green' => 0.4,
                                    'blue' => 0.6
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
                ]),
                // Auto-resize columns
                new \Google\Service\Sheets\Request([
                    'autoResizeDimensions' => [
                        'dimensions' => [
                            'sheetId' => $sheetId,
                            'dimension' => 'COLUMNS',
                            'startIndex' => 0,
                            'endIndex' => 20
                        ]
                    ]
                ])
            ];

            $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);

            $this->sheetsService->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);

        } catch (\Exception $e) {
            Log::warning('Sheet formatting failed', [
                'sheet_name' => $sheetName,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function addMetadata($sheetName, $filename, $recordCount)
    {
        try {
            // Add metadata to the end of the sheet
            $metadataRange = $sheetName . '!A' . ($recordCount + 3); // 3 rows below data

            $metadata = [
                ['Metadata'],
                ['Generated At:', Carbon::now()->format('Y-m-d H:i:s')],
                ['Filename:', $filename],
                ['Total Records:', $recordCount],
                ['Generated By:', 'Laravel SuratKuasa System']
            ];

            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $metadata
            ]);

            $this->sheetsService->spreadsheets_values->update(
                $this->spreadsheetId,
                $metadataRange,
                $body,
                ['valueInputOption' => 'RAW']
            );

        } catch (\Exception $e) {
            Log::warning('Failed to add metadata', [
                'sheet_name' => $sheetName,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getSheetId($sheetName)
    {
        try {
            $spreadsheet = $this->sheetsService->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();

            foreach ($sheets as $sheet) {
                if ($sheet->getProperties()->getTitle() === $sheetName) {
                    return $sheet->getProperties()->getSheetId();
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting sheet ID', [
                'sheet_name' => $sheetName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create backup of spreadsheet to Drive folder
     */
    public function backupSpreadsheet()
    {
        try {
            if (!config('services.google.drive_folder_id')) {
                return ['success' => false, 'error' => 'Drive folder ID not configured'];
            }

            $fileName = 'SuratKuasa_Backup_' . Carbon::now()->format('Y-m-d_H-i-s');

            $copiedFile = new \Google\Service\Drive\DriveFile([
                'name' => $fileName,
                'parents' => [config('services.google.drive_folder_id')]
            ]);

            $result = $this->driveService->files->copy($this->spreadsheetId, $copiedFile);

            Log::info('Spreadsheet backup created', [
                'backup_id' => $result->getId(),
                'backup_name' => $fileName
            ]);

            return [
                'success' => true,
                'backup_id' => $result->getId(),
                'backup_name' => $fileName,
                'backup_url' => "https://docs.google.com/spreadsheets/d/{$result->getId()}"
            ];

        } catch (\Exception $e) {
            Log::error('Spreadsheet backup failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get spreadsheet information
     */
    public function getSpreadsheetInfo()
    {
        try {
            $spreadsheet = $this->sheetsService->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();

            $sheetInfo = [];
            foreach ($sheets as $sheet) {
                $properties = $sheet->getProperties();
                $sheetInfo[] = [
                    'id' => $properties->getSheetId(),
                    'title' => $properties->getTitle(),
                    'row_count' => $properties->getGridProperties()->getRowCount(),
                    'column_count' => $properties->getGridProperties()->getColumnCount()
                ];
            }

            return [
                'success' => true,
                'spreadsheet_id' => $this->spreadsheetId,
                'title' => $spreadsheet->getProperties()->getTitle(),
                'sheets' => $sheetInfo,
                'url' => "https://docs.google.com/spreadsheets/d/{$this->spreadsheetId}"
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
