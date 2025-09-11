<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Export SuratKuasa data setiap jam 4 sore (16:00)
        // Default: export hanya data hari itu
        $schedule->command('surat-kuasa:export --type=daily')
                ->dailyAt('16:00')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->runInBackground()
                ->emailOutputOnFailure(config('mail.admin_email'))
                ->onSuccess(function () {
                    \Log::info('Daily SuratKuasa export completed successfully');
                })
                ->onFailure(function () {
                    \Log::error('Daily SuratKuasa export failed');
                });

        // Export semua data setiap hari Minggu jam 2 siang (untuk backup penuh)
        $schedule->command('surat-kuasa:export --type=all')
                ->weeklyOn(0, '14:00') // 0 = Sunday
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->runInBackground()
                ->emailOutputOnFailure(config('mail.admin_email'))
                ->onSuccess(function () {
                    \Log::info('Weekly full SuratKuasa export completed successfully');
                })
                ->onFailure(function () {
                    \Log::error('Weekly full SuratKuasa export failed');
                });

        // Alternatif: Export berdasarkan kondisi tertentu
        // Jika ingin export all data saat hari pertama setiap bulan
        $schedule->command('surat-kuasa:export --type=all')
                ->monthlyOn(1, '16:00')
                ->timezone('Asia/Jakarta')
                ->withoutOverlapping()
                ->runInBackground()
                ->when(function () {
                    // Tambahan kondisi jika diperlukan
                    return config('app.enable_monthly_full_export', true);
                });

        // Cleanup old CSV files (hapus file CSV yang lebih dari 30 hari)
        $schedule->call(function () {
            $exportsPath = storage_path('app/exports');

            if (is_dir($exportsPath)) {
                $files = glob($exportsPath . '/*.csv');
                $thirtyDaysAgo = now()->subDays(30)->timestamp;

                foreach ($files as $file) {
                    if (filemtime($file) < $thirtyDaysAgo) {
                        unlink($file);
                        \Log::info("Deleted old export file: " . basename($file));
                    }
                }
            }
        })
        ->daily()
        ->at('02:00')
        ->timezone('Asia/Jakarta')
        ->name('cleanup-old-exports');

        // Health check - cek apakah export berjalan dengan baik
        $schedule->call(function () {
            $today = now()->format('Y-m-d');
            $exportsPath = storage_path('app/exports');

            // Cek apakah ada file export hari ini
            $todayFiles = glob($exportsPath . "/surat_kuasa_export_daily_{$today}*.csv");

            if (empty($todayFiles)) {
                \Log::warning('No daily export file found for today', ['date' => $today]);

                // Optional: kirim notifikasi ke admin
                // Mail::to(config('mail.admin_email'))->send(new ExportHealthCheckFailed($today));
            } else {
                \Log::info('Daily export health check passed', [
                    'date' => $today,
                    'files_found' => count($todayFiles)
                ]);
            }
        })
        ->dailyAt('17:00') // Cek 1 jam setelah export seharusnya selesai
        ->timezone('Asia/Jakarta')
        ->name('export-health-check');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
